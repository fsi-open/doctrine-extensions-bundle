<?php

/**
 * (c) Fabryka Stron Internetowych sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\Twig\Extension;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AssetsSpec extends ObjectBehavior
{
    /**
     * @param \Twig_Environment $environment
     * @param \Symfony\Bundle\TwigBundle\Extension\AssetsExtension $assets
     */
    function let($environment, $assets)
    {
        $this->beConstructedWith(__DIR__ . '/tmp');
        $environment->hasExtension('assets')->shouldBeCalled()->willReturn(true);
        $environment->getExtension('assets')->shouldBeCalled()->willReturn($assets);
        $environment->getGlobals()->shouldBeCalled()->willReturn(array());
        $this->initRuntime($environment);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('FSi\Bundle\DoctrineExtensionsBundle\Twig\Extension\Assets');
    }

    function it_is_twig_extension()
    {
        $this->shouldBeAnInstanceOf('Twig_Extension');
    }

    function it_have_fsi_url_name()
    {
        $this->getName()->shouldReturn('fsi_assets');
    }

    function it_have_fsi_file_asset_function()
    {
        $this->getFunctions()->shouldHaveFunction('fsi_file_asset');
    }

    function it_have_fsi_file_basename_filter()
    {
        $this->getFilters()->shouldHaveFilter('fsi_file_basename');
    }

    /**
     * @param \Symfony\Bundle\TwigBundle\Extension\AssetsExtension $assets
     * @param \FSi\DoctrineExtensions\Uploadable\File $file
     * @param \Gaufrette\Filesystem $filesystem
     * @param \Gaufrette\Adapter\Local $adapter
     */
    function it_generate_url_for_fsi_file_with_local_adapter($assets, $file, $filesystem, $adapter)
    {
        $assets->getAssetUrl('TestFolder/File/file.jpg')->shouldBeCalled()->willReturn('/TestFolder/File/file.jpg');

        $file->getKey()->shouldBeCalled()->willReturn('TestFolder/File/file.jpg');
        $file->getFilesystem()->shouldBeCalled()->willReturn($filesystem);
        $filesystem->getAdapter()->willReturn($adapter);

        $this->fileAsset($file)->shouldReturn('/TestFolder/File/file.jpg');
    }

    /**
     * @param \Symfony\Bundle\TwigBundle\Extension\AssetsExtension $assets
     * @param \FSi\DoctrineExtensions\Uploadable\File $file
     * @param \Gaufrette\Filesystem $filesystem
     * @param \Gaufrette\Adapter\Cache $adapter
     */
    function it_generate_url_for_fsi_file_with_cache_adapter($assets, $file, $filesystem, $adapter)
    {
        $assets->getAssetUrl('TestFolder/File/file.jpg')->shouldBeCalled()->willReturn('/TestFolder/File/file.jpg');

        $file->getKey()->shouldBeCalled()->willReturn('TestFolder/File/file.jpg');
        $file->getFilesystem()->shouldBeCalled()->willReturn($filesystem);
        $filesystem->getAdapter()->willReturn($adapter);

        $this->fileAsset($file)->shouldReturn('/TestFolder/File/file.jpg');
    }

    /**
     * @param \Symfony\Bundle\TwigBundle\Extension\AssetsExtension $assets
     * @param \FSi\DoctrineExtensions\Uploadable\File $file
     * @param \Gaufrette\Filesystem $filesystem
     * @param \Gaufrette\Adapter\Local $adapter
     */
    function it_generate_url_with_file_path_prefix($assets, $file, $filesystem, $adapter)
    {
        $assets->getAssetUrl('uploaded/TestFolder/File/file.jpg')->shouldBeCalled()->willReturn('/uploaded/TestFolder/File/file.jpg');

        $file->getKey()->shouldBeCalled()->willReturn('TestFolder/File/file.jpg');
        $file->getFilesystem()->shouldBeCalled()->willReturn($filesystem);
        $filesystem->getAdapter()->willReturn($adapter);

        $this->fileAsset($file, 'uploaded')->shouldReturn('/uploaded/TestFolder/File/file.jpg');
    }

    /**
     * @param \Symfony\Bundle\TwigBundle\Extension\AssetsExtension $assets
     * @param \FSi\DoctrineExtensions\Uploadable\File $file
     * @param \Gaufrette\Filesystem $filesystem
     * @param \Gaufrette\Adapter\Local $adapter
     */
    function it_generate_url_with_file_path_prefix_that_should_be_trimed($assets, $file, $filesystem, $adapter)
    {
        $assets->getAssetUrl('uploaded/TestFolder/File/file.jpg')->shouldBeCalled()->willReturn('/uploaded/TestFolder/File/file.jpg');

        $file->getKey()->shouldBeCalled()->willReturn('TestFolder/File/file.jpg');
        $file->getFilesystem()->shouldBeCalled()->willReturn($filesystem);
        $filesystem->getAdapter()->willReturn($adapter);

        $this->fileAsset($file, '/uploaded/')->shouldReturn('/uploaded/TestFolder/File/file.jpg');
    }

    /**
     * @param \Symfony\Bundle\TwigBundle\Extension\AssetsExtension $assets
     * @param \FSi\DoctrineExtensions\Uploadable\File $file
     * @param \Gaufrette\Filesystem $filesystem
     * @param \Gaufrette\Adapter $adapter
     */
    function it_generate_url_for_fsi_file_with_external_adapter($assets, $file, $filesystem, $adapter)
    {
        $assets->getAssetUrl('TestFolder/File/file.jpg')->shouldBeCalled()->willReturn('/TestFolder/File/file.jpg');

        $file->getKey()->shouldBeCalled()->willReturn('TestFolder/File/file.jpg');
        $file->getFilesystem()->shouldBeCalled()->willReturn($filesystem);
        $filesystem->getAdapter()->willReturn($adapter);

        $file->getContent()->shouldBeCalled()->willReturn('file content');

        $this->fileAsset($file)->shouldReturn('/TestFolder/File/file.jpg');
    }

    /**
     * @param \Twig_Environment $environment
     * @param \Symfony\Bundle\TwigBundle\Extension\AssetsExtension $assets
     * @param \FSi\DoctrineExtensions\Uploadable\File $file
     * @param \Gaufrette\Filesystem $filesystem
     * @param \Gaufrette\Adapter\Local $adapter
     */
    function it_generate_url_with_prefix_from_globals($environment, $assets, $file, $filesystem, $adapter)
    {
        $environment->getGlobals()->willReturn(array(
            'fsi_file_prefix' => 'uploaded'
        ));
        $this->initRuntime($environment);

        $assets->getAssetUrl('uploaded/TestFolder/File/file.jpg')->shouldBeCalled()->willReturn('/uploaded/TestFolder/File/file.jpg');

        $file->getKey()->shouldBeCalled()->willReturn('TestFolder/File/file.jpg');
        $file->getFilesystem()->shouldBeCalled()->willReturn($filesystem);
        $filesystem->getAdapter()->willReturn($adapter);

        $this->fileAsset($file)->shouldReturn('/uploaded/TestFolder/File/file.jpg');
    }

    /**
     * @param \Twig_Environment $environment
     * @param \Symfony\Bundle\TwigBundle\Extension\AssetsExtension $assets
     * @param \FSi\DoctrineExtensions\Uploadable\File $file
     * @param \Gaufrette\Filesystem $filesystem
     * @param \Gaufrette\Adapter\Local $adapter
     */
    function it_generate_url_with_passed_prefix_even_if_there_is_a_prefix_in_globals($environment, $assets, $file, $filesystem, $adapter)
    {
        $environment->getGlobals()->willReturn(array(
            'fsi_file_prefix' => 'uploaded'
        ));
        $this->initRuntime($environment);

        $assets->getAssetUrl('my_prefix/TestFolder/File/file.jpg')->shouldBeCalled()->willReturn('/my_prefix/TestFolder/File/file.jpg');

        $file->getKey()->shouldBeCalled()->willReturn('TestFolder/File/file.jpg');
        $file->getFilesystem()->shouldBeCalled()->willReturn($filesystem);
        $filesystem->getAdapter()->willReturn($adapter);

        $this->fileAsset($file, 'my_prefix')->shouldReturn('/my_prefix/TestFolder/File/file.jpg');
    }

    /**
     * @param \FSi\DoctrineExtensions\Uploadable\File $file
     */
    function it_generate_fsi_file_basename($file)
    {
        $file->getName()->shouldBeCalled()->willReturn('TestFolder/File/file.jpg');

        $this->fileBasename($file)->shouldReturn('file.jpg');
    }

    public function getMatchers()
    {
        return array(
            'haveFunction' => function($subject, $key) {
                foreach ($subject as $function) {
                    if ($function instanceof \Twig_SimpleFunction) {
                        if ($function->getName() == $key) {
                            return true;
                        }
                    }
                }

                return false;
            },
            'haveFilter' => function($subject, $key) {
                foreach ($subject as $filter) {
                    if ($filter instanceof \Twig_SimpleFilter) {
                        if ($filter->getName() == $key) {
                            return true;
                        }
                    }
                }

                return false;
            }
        );
    }

    function letgo()
    {
        if (file_exists(__DIR__ . '/tmp')) {
            self::deleteRecursive(__DIR__ . '/tmp');
            rmdir(__DIR__ . '/tmp');
        }
    }

    public static function deleteRecursive($path)
    {
        foreach (new \DirectoryIterator($path) as $file) {
            if ($file->isDot()) {
                continue;
            }

            $filename = $path . DIRECTORY_SEPARATOR . $file->getFilename();

            if ($file->isDir()) {
                self::deleteRecursive($filename);
                rmdir($filename);
            } else {
                unlink($filename);
            }
        }
    }
}
