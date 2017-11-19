<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\Tests\Form\Type\FSi;

use FSi\Bundle\DoctrineExtensionsBundle\Tests\Fixtures\Entity\Article;
use FSi\Bundle\DoctrineExtensionsBundle\Tests\Fixtures\Form\Extension\FSiFileExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class RemovableFileTypeTest extends FormTypeTest
{
    public function getExtensions()
    {
        return [
            new FSiFileExtension()
        ];
    }

    public function testFormRendering()
    {
        $file = $this->getFileMock();

        $article = new Article();
        $article->setFile($file);

        $form = $this->createTestForm($article);

        $html = $this->twig->render('form_with_fsi_file.html.twig', ['form' => $form->createView()]);
        $this->assertSame($this->getExpectedHtml('form_with_fsi_removable_file.html'), $html);
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
        $file = $this->getMockBuilder('FSi\DoctrineExtensions\Uploadable\File')
            ->disableOriginalConstructor()
            ->getMock();

        $file->expects($this->any())
            ->method('getFilesystem')
            ->will($this->returnCallback(function() {
                $fileSystem = $this->getMockBuilder('FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable\Filesystem')
                    ->disableOriginalConstructor()
                    ->getMock();

                $fileSystem->expects($this->any())
                    ->method('getBaseUrl')
                    ->will($this->returnValue('/uploaded/'));

                $fileSystem->expects($this->any())
                    ->method('getAdapter')
                    ->will($this->returnCallback(function() {
                        return $this->getMockBuilder('Gaufrette\Adapter\Local')
                            ->disableOriginalConstructor()
                            ->getMock();
                    }));

                return $fileSystem;
            }));

        $file->expects($this->any())
            ->method('getKey')
            ->will($this->returnValue('Article/file/1/image name.jpg'));

        $file->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('Article/file/1/image name.jpg'));

        return $file;
    }

    private function createTestForm(Article $article): FormInterface
    {
        $form = $this->factory->create(
            $this->isSymfony3() ? 'Symfony\Component\Form\Extension\Core\Type\FormType' : 'form',
            $article
        );

        $form->add('file', $this->isSymfony3() ? 'FSi\Bundle\DoctrineExtensionsBundle\Form\Type\FSi\RemovableFileType' : 'fsi_removable_file');

        return $form;
    }
}
