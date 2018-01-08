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
use Symfony\Bridge\Twig\Command\DebugCommand;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Bridge\Twig\Tests\Extension\Fixtures\StubFilesystemLoader;
use Symfony\Bridge\Twig\Tests\Extension\Fixtures\StubTranslator;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\UrlPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\Test\FormIntegrationTestCase;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig_Environment;
use Twig_RuntimeLoaderInterface;

abstract class FormTypeTest extends FormIntegrationTestCase
{
    /**
     * @var Twig_Environment
     */
    protected $twig;

    public function setUp()
    {
        parent::setUp();

        $paths = [
            __DIR__ . '/../../../../Resources/views/Form',
            __DIR__ . '/../../../Resources/views',
            (file_exists(VENDOR_DIR . '/symfony/twig-bridge/Resources/views'))
                ? VENDOR_DIR . '/symfony/twig-bridge/Resources/views'
                : VENDOR_DIR . '/symfony/twig-bridge/Symfony/Bridge/Twig/Resources/views'
        ];

        $loader = new StubFilesystemLoader($paths);

        $twig = new Twig_Environment($loader, ['strict_variables' => true]);
        $twig->addGlobal('global', '');
        $twig->addExtension(new TranslationExtension(new StubTranslator()));
        $twig->addExtension(new FilesExtension(new FSiFilePathResolver()));
        $rendererEngine = new TwigRendererEngine([
            'form_div_layout.html.twig',
            'Form/form_div_layout.html.twig'
        ], $twig);
        if (method_exists(DebugCommand::class, 'getLoaderPaths')) {
            $renderer = new FormRenderer($rendererEngine);
        } else {
            $renderer = new TwigRenderer(
                $rendererEngine,
                $this->getMockBuilder(CsrfTokenManagerInterface::class)->getMock()
            );
        }

        $runtimeLoader = $this->getMockBuilder(Twig_RuntimeLoaderInterface::class)->getMock();
        $runtimeLoader->expects($this->any())->method('load')->will($this->returnValueMap([
            [TwigRenderer::class, $renderer],
            [FormRenderer::class, $renderer],
        ]));
        $twig->addRuntimeLoader($runtimeLoader);
        $twig->addExtension(new FormExtension($renderer));
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
