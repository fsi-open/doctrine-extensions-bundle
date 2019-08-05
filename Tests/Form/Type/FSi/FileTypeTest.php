<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DoctrineExtensionsBundle\Tests\Form\Type\FSi;

use FSi\Bundle\DoctrineExtensionsBundle\Form\Type\FSi\FileType;
use FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable\Filesystem;
use FSi\Bundle\DoctrineExtensionsBundle\Resolver\FSiFilePathResolver;
use FSi\Bundle\DoctrineExtensionsBundle\Tests\Fixtures\Entity\Article;
use FSi\Bundle\DoctrineExtensionsBundle\Tests\Fixtures\Form\Extension\FSiFileExtension;
use FSi\DoctrineExtensions\Uploadable\File;
use Gaufrette\Adapter\Local;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FileTypeTest extends FormTypeTest
{
    public function getExtensions()
    {
        return [
            new FSiFileExtension(
                $this->getMockBuilder(UrlGeneratorInterface::class)->disableOriginalConstructor()->getMock(),
                new FSiFilePathResolver()
            ),
        ];
    }

    public function testFormRendering()
    {
        $file = $this->getFileMock();

        $article = new Article();
        $article->setFile($file);

        $form = $this->factory->create($this->isSymfony3() ? FormType::class : 'form', $article);
        $form->add('file', $this->isSymfony3() ? FileType::class : 'fsi_file');

        $html = $this->twig->render('form_with_fsi_file.html.twig', [
            'form' => $form->createView()
        ]);
        $this->assertSame(
            $this->getExpectedHtml('form_with_fsi_file.html'),
            str_replace('<div >', '<div>', $html)
        );
    }

    public function testFormRenderingWithCustomFileUrl()
    {
        $file = $this->getFileMock();

        $article = new Article();
        $article->setFile($file);

        $form = $this->factory->create(
            $this->isSymfony3() ? FormType::class : 'form',
            $article
        );

        $form->add('file', $this->isSymfony3() ? FileType::class : 'fsi_file', [
            'file_url' => function (UrlGeneratorInterface $urlGenerator, FormInterface $form) {
                return 'constant_file_url';
            },
        ]);

        $html = $this->twig->render('form_with_fsi_file.html.twig', [
            'form' => $form->createView()
        ]);
        $this->assertSame(
            $this->getExpectedHtml('form_with_fsi_file_and_constant_url.html'),
            str_replace('<div >', '<div>', $html)
        );
    }

    private function getFileMock()
    {
        $file = $this->getMockBuilder(File::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $file->expects($this->any())
            ->method('getFilesystem')
            ->will(
                $this->returnCallback(
                    function () {
                        $fileSystem = $this->getMockBuilder(Filesystem::class)
                            ->disableOriginalConstructor()
                            ->getMock()
                        ;

                        $fileSystem->expects($this->any())
                            ->method('getBaseUrl')
                            ->will($this->returnValue('/uploaded/'))
                        ;

                        $fileSystem->expects($this->any())
                            ->method('getAdapter')
                            ->will(
                                $this->returnCallback(
                                    function () {
                                        return $this->getMockBuilder(Local::class)
                                            ->disableOriginalConstructor()
                                            ->getMock()
                                        ;
                                    }
                                )
                            );

                        return $fileSystem;
                    }
                )
            );

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
}
