<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\Tests\Form\Type\FSi;

use FSi\Bundle\DoctrineExtensionsBundle\Resolver\FSiFilePathResolver;
use FSi\Bundle\DoctrineExtensionsBundle\Twig\Extension\Assets;
use FSi\Bundle\DoctrineExtensionsBundle\Twig\Extension\FSi\File as FileTwigExtension;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Bridge\Twig\Tests\Extension\Fixtures\StubFilesystemLoader;
use Symfony\Bridge\Twig\Tests\Extension\Fixtures\StubTranslator;
use Symfony\Bundle\TwigBundle\Extension\AssetsExtension;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\UrlPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
// prior to 2.7 asset component was part of FrameworkBundle
use Symfony\Component\Templating\Asset\UrlPackage as LegacyUrlPackage;
use Symfony\Component\Form\Test\FormIntegrationTestCase;

abstract class FormTypeTest extends FormIntegrationTestCase
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    public function setUp()
    {
        parent::setUp();

        $paths = array(
            __DIR__ . '/../../../../Resources/views/Form',
            __DIR__ . '/../../../Resources/views',
            (file_exists(VENDOR_DIR . '/symfony/twig-bridge/Resources/views'))
                ? VENDOR_DIR . '/symfony/twig-bridge/Resources/views'
                : VENDOR_DIR . '/symfony/twig-bridge/Symfony/Bridge/Twig/Resources/views'
        );

        $loader = new StubFilesystemLoader($paths);

        $twig = new \Twig_Environment($loader, array('strict_variables' => true));
        $twig->addGlobal('global', '');
        $twig->addExtension(new TranslationExtension(new StubTranslator()));
        $twig->addExtension(new FileTwigExtension());
        $twig->addExtension(new Assets(new FSiFilePathResolver('/adapter/path', 'uploaded')));
        $rendererEngine = new TwigRendererEngine(array(
            'form_div_layout.html.twig',
            'Form/form_div_layout.html.twig'
        ), $twig);
        $renderer = new TwigRenderer(
            $rendererEngine,
            $this->getMock('\Symfony\Component\Security\Csrf\CsrfTokenManagerInterface')
        );

        if (class_exists('Symfony\Bridge\Twig\Extension\AssetExtension')) {
            $runtimeLoader = $this->getMock('Twig_RuntimeLoaderInterface');
            $runtimeLoader->expects($this->any())->method('load')->will($this->returnValueMap(array(
                array('Symfony\Bridge\Twig\Form\TwigRenderer', $renderer),
            )));
            $twig->addRuntimeLoader($runtimeLoader);

            $twig->addExtension(new FormExtension());
            $twig->addExtension(new AssetExtension(
                new Packages(
                    new UrlPackage(array('http://local.dev/'), new EmptyVersionStrategy())
                )
            ));
        } else {
            $twig->addExtension(new FormExtension($renderer));
            $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
            $container->expects($this->any())
                ->method('get')
                ->with('templating.helper.assets')
                ->will($this->returnValue(new LegacyUrlPackage()));

            $request = $this->getMock('Symfony\Component\Routing\RequestContext');
            $twig->addExtension(new AssetsExtension($container, $request));
        }

        $this->twig = $twig;
    }

    /**
     * @param string $filename
     * @return string
     */
    protected function getExpectedHtml($filename)
    {
        $path = __DIR__ . '/../../../Resources/views/' . $filename;
        if (!file_exists($path)) {
            throw new \RuntimeException(sprintf('Invalid expected html file path "%s"', $path));
        }

        return file_get_contents($path);
    }
}
