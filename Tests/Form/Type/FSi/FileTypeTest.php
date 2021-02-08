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
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FileTypeTest extends FormTypeTest
{
    public function getExtensions(): array
    {
        return [
            new FSiFileExtension(
                $this->getMockBuilder(UrlGeneratorInterface::class)->disableOriginalConstructor()->getMock(),
                new FSiFilePathResolver()
            ),
        ];
    }

    public function testFormRendering(): void
    {
        $file = $this->getFileMock();

        $article = new Article();
        $article->setFile($file);

        $form = $this->factory->create(FormType::class, $article);
        $form->add('file', FileType::class);

        $html = $this->twig->render('form_with_fsi_file.html.twig', [
            'form' => $form->createView()
        ]);
        self::assertSame(
            $this->getExpectedHtml('form_with_fsi_file.html'),
            str_replace('<div >', '<div>', $html)
        );
    }

    public function testFormRenderingWithCustomFileUrl(): void
    {
        $file = $this->getFileMock();

        $article = new Article();
        $article->setFile($file);

        $form = $this->factory->create(FormType::class, $article);

        $form->add('file', FileType::class, [
            'file_url' => function (UrlGeneratorInterface $urlGenerator, FormInterface $form) {
                return 'constant_file_url';
            },
        ]);

        $html = $this->twig->render('form_with_fsi_file.html.twig', [
            'form' => $form->createView()
        ]);
        self::assertSame(
            $this->getExpectedHtml('form_with_fsi_file_and_constant_url.html'),
            str_replace('<div >', '<div>', $html)
        );
    }

    /**
     * @return File&MockObject
     */
    private function getFileMock(): File
    {
        $file = $this->createMock(File::class);

        $file->method('getFilesystem')
            ->willReturnCallback(
                function (): MockObject {
                    $fileSystem = $this->createMock(Filesystem::class);
                    $fileSystem->method('getBaseUrl')->willReturn('/uploaded/');
                    $fileSystem->method('getAdapter')
                        ->willReturnCallback(
                            function (): MockObject {
                                return $this->createMock(Local::class);
                            }
                        );

                    return $fileSystem;
                }
            );

        $file->method('getKey')->willReturn('Article/file/1/image name.jpg');
        $file->method('getName')->willReturn('Article/file/1/image name.jpg');

        return $file;
    }
}
