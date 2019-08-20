<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DoctrineExtensionsBundle\Tests\Form\Type\FSi;

use FSi\Bundle\DoctrineExtensionsBundle\Form\Type\FSi\RemovableFileType;
use FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable\Filesystem;
use FSi\Bundle\DoctrineExtensionsBundle\Resolver\FSiFilePathResolver;
use FSi\Bundle\DoctrineExtensionsBundle\Tests\Fixtures\Entity\Article;
use FSi\Bundle\DoctrineExtensionsBundle\Tests\Fixtures\Form\Extension\FSiFileExtension;
use FSi\DoctrineExtensions\Uploadable\File;
use Gaufrette\Adapter\Local;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RemovableFileTypeTest extends FormTypeTest
{
    public function getExtensions()
    {
        return [
            new FSiFileExtension(
                $this->createMock(UrlGeneratorInterface::class),
                new FSiFilePathResolver()
            ),
        ];
    }

    public function testFormRendering()
    {
        $file = $this->getFileMock();

        $article = new Article();
        $article->setFile($file);

        $form = $this->createTestForm($article);

        $html = $this->twig->render('form_with_fsi_file.html.twig', ['form' => $form->createView()]);
        $this->assertSame(
            $this->getExpectedHtml('form_with_fsi_removable_file.html'),
            str_replace('<div >', '<div>', $html)
        );
    }

    public function testFormSubmission()
    {
        $file = $this->getFileMock();

        $article = new Article();
        $article->setFile($file);

        $form = $this->createTestForm($article);

        $request = new Request([], ['form' => ['file' => ['file' => null]]]);
        $request->setMethod('POST');
        $form->handleRequest($request);

        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->isValid());
        $this->assertSame($article->getFile(), $file);
    }

    private function getFileMock()
    {
        $file = $this->getMockBuilder(File::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $file->expects($this->any())
            ->method('getFilesystem')
            ->will($this->returnCallback(function() {
                $fileSystem = $this->createMock(Filesystem::class);
                $fileSystem->expects($this->any())
                    ->method('getBaseUrl')
                    ->will($this->returnValue('/uploaded/'))
                ;

                $fileSystem->expects($this->any())
                    ->method('getAdapter')
                    ->will($this->returnCallback(function() {
                        return $this->createMock(Local::class);
                    }));

                return $fileSystem;
            }));

        $file->expects($this->any())
            ->method('getKey')
            ->will($this->returnValue('Article/file/1/image name.jpg'))
        ;

        $file->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('Article/file/1/image name.jpg'))
        ;

        return $file;
    }

    private function createTestForm(Article $article): FormInterface
    {
        $form = $this->factory->create(FormType::class, $article);
        $form->add('file', RemovableFileType::class);

        return $form;
    }
}
