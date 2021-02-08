<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DoctrineExtensionsBundle\Tests\Form\Type\FSi;

use FSi\Bundle\DoctrineExtensionsBundle\Resolver\FSiFilePathResolver;
use FSi\Bundle\DoctrineExtensionsBundle\Twig\FilesExtension;
use RuntimeException;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Bridge\Twig\Tests\Extension\Fixtures\StubFilesystemLoader;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\UrlPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\Test\FormIntegrationTestCase;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Contracts\Translation\TranslatorTrait;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use function class_exists;
use function interface_exists;
use function trait_exists;

abstract class FormTypeTest extends FormIntegrationTestCase
{
    /**
     * @var Environment
     */
    protected $twig;

    public function setUp(): void
    {
        parent::setUp();

        $paths = [
            __DIR__ . '/../../../../Resources/views/Form',
            __DIR__ . '/../../../Resources/views',
            VENDOR_DIR . '/symfony/twig-bridge/Resources/views'
        ];

        if (class_exists(StubFilesystemLoader::class)) {
            $loader = new StubFilesystemLoader($paths);
        } else {
            $loader = new FilesystemLoader($paths);
        }

        $twig = new Environment($loader, ['strict_variables' => true]);
        $twig->addGlobal('global', '');
        if (true === interface_exists(TranslatorInterface::class) && true === trait_exists(TranslatorTrait::class)) {
            $twig->addExtension(new TranslationExtension(
                new class implements TranslatorInterface{
                    use TranslatorTrait;
                }
            ));
        } elseif (true === class_exists(Translator::class)) {
            $twig->addExtension(new TranslationExtension(new Translator('EN')));
        }
        $twig->addExtension(new FilesExtension(new FSiFilePathResolver()));
        $rendererEngine = new TwigRendererEngine([
            'form_div_layout.html.twig',
            'Form/form_div_layout.html.twig'
        ], $twig);

        $renderer = new FormRenderer($rendererEngine);
        $runtimeLoader = $this->createMock(RuntimeLoaderInterface::class);
        $runtimeLoader->method('load')->willReturnMap([
            [FormRenderer::class, $renderer],
        ]);
        $twig->addRuntimeLoader($runtimeLoader);
        $twig->addExtension(new FormExtension());
        $twig->addExtension(new AssetExtension(
            new Packages(new UrlPackage(['http://local.dev/'], new EmptyVersionStrategy()))
        ));

        $this->twig = $twig;
    }

    /**
     * @throws RuntimeException
     */
    protected function getExpectedHtml(string $filename): string
    {
        $path = __DIR__ . '/../../../Resources/views/' . $filename;
        if (!file_exists($path)) {
            throw new RuntimeException(sprintf('Invalid expected html file path "%s"', $path));
        }

        return trim(file_get_contents($path));
    }

    protected function isSymfony3(): bool
    {
        return method_exists(AbstractType::class, 'getBlockPrefix');
    }
}
