<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\Tests\Form\Type\FSi;

use FSi\Bundle\DoctrineExtensionsBundle\Resolver\FSiFilePathResolver;
use FSi\Bundle\DoctrineExtensionsBundle\Tests\Fixtures\Entity\Article;
use FSi\Bundle\DoctrineExtensionsBundle\Tests\Fixtures\Form\Extension\FSiFileExtension;
use FSi\Bundle\DoctrineExtensionsBundle\Twig\Extension\Assets;
use FSi\Bundle\DoctrineExtensionsBundle\Twig\Extension\FSi\File as FileTwigExtension;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Bridge\Twig\Tests\Extension\Fixtures\StubFilesystemLoader;
use Symfony\Bridge\Twig\Tests\Extension\Fixtures\StubTranslator;
use Symfony\Bundle\TwigBundle\Extension\AssetsExtension;
use Symfony\Component\Form\Test\FormIntegrationTestCase;
use Symfony\Component\Templating\Asset\UrlPackage;

class FileTypeTest extends FormIntegrationTestCase
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    public function setUp()
    {
        parent::setUp();

        $loader = new StubFilesystemLoader(array(
            __DIR__ . '/../../../../vendor/symfony/twig-bridge/Symfony/Bridge/Twig/Resources/views',
            __DIR__ . '/../../../../Resources/views/Form',
            __DIR__ . '/../../../Resources/views', // templates used in tests
        ));

        $rendererEngine = new TwigRendererEngine(array(
            'form_div_layout.html.twig',
            'Form/form_div_layout.html.twig'
        ));
        $renderer = new TwigRenderer($rendererEngine, $this->getMock('Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface'));

        $twig = new \Twig_Environment($loader, array('strict_variables' => true));
        $twig->addGlobal('global', '');
        $twig->addExtension(new TranslationExtension(new StubTranslator()));
        $twig->addExtension(new FormExtension($renderer));
        $twig->addExtension(new Assets(new FSiFilePathResolver('/adapter/path', 'uploaded')));
        $twig->addExtension(new FileTwigExtension());


        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container->expects($this->any())
            ->method('get')
            ->with('templating.helper.assets')
            ->will($this->returnValue(new UrlPackage()));

        $request = $this->getMock('Symfony\Component\Routing\RequestContext');
        $twig->addExtension(new AssetsExtension($container, $request));
        $this->twig = $twig;
    }

    /**
     * @return \FSi\Bundle\DoctrineExtensionsBundle\Tests\Fixtures\Form\Extension\FSiFileExtension
     */
    public function getExtensions()
    {
        return array(
            new FSiFileExtension()
        );
    }

    public function testFormRendering()
    {
        $self = $this;

        $file = $this->getMockBuilder('FSi\DoctrineExtensions\Uploadable\File')
            ->disableOriginalConstructor()
            ->getMock();

        $file->expects($this->any())
            ->method('getFilesystem')
            ->will($this->returnCallback(function() use ($self) {
                $fileSystem = $self->getMockBuilder('FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable\Filesystem')
                    ->disableOriginalConstructor()
                    ->getMock();

                $fileSystem->expects($self->any())
                    ->method('getBaseUrl')
                    ->will($self->returnValue('/uploaded/'));

                $fileSystem->expects($self->any())
                    ->method('getAdapter')
                    ->will($self->returnCallback(function() use ($self) {
                        $adapter = $self->getMockBuilder('Gaufrette\Adapter\Local')
                            ->disableOriginalConstructor()
                            ->getMock();

                        return $adapter;
                    }));

                return $fileSystem;
            }));

        $file->expects($this->any())
            ->method('getKey')
            ->will($this->returnValue('Article/file/1/image name.jpg'));

        $file->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('Article/file/1/image name.jpg'));

        $article = new Article();
        $article->setFile($file);

        $form = $this->factory->create('form', $article);

        $form->add('file', 'fsi_file');

        $html = $this->twig->render('form_with_fsi_file.html.twig', array(
            'form' => $form->createView()
        ));
        $this->assertSame($html, $this->getExpectedHtml('form_with_fsi_file.html'));
    }

    /**
     * @param string $filename
     * @return string
     */
    private function getExpectedHtml($filename)
    {
        $path = __DIR__ . '/../../../Resources/views/' . $filename;
        if (!file_exists($path)) {
            throw new \RuntimeException(sprintf('Invalid expected html file path "%s"', $path));
        }

        return file_get_contents($path);
    }
}
