<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\FSi\Bundle\DoctrineExtensionsBundle;

use FSi\DoctrineExtensions\Uploadable\File;
use Gaufrette\Adapter\Local;
use Gaufrette\Filesystem;
use PhpSpec\ObjectBehavior;
use Twig_Environment;

class FSiFilePathResolverSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(__DIR__ . '/tmp', 'uploaded');
    }

    function it_generate_url_for_fsi_file_with_local_adapter(File $file, Filesystem $filesystem, Local $adapter)
    {
        $file->getKey()->shouldBeCalled()->willReturn('TestFolder/File/file.jpg');
        $file->getFilesystem()->shouldBeCalled()->willReturn($filesystem);
        $filesystem->getAdapter()->willReturn($adapter);

        $this->fileAsset($file)->shouldReturn('/TestFolder/File/file.jpg');
        $this->filePath($file)->shouldReturn('/TestFolder/File/file.jpg');
    }

    function it_generate_url_for_fsi_file_with_cache_adapter(File $file, Filesystem $filesystem, Local $adapter)
    {
        $file->getKey()->shouldBeCalled()->willReturn('TestFolder/File/file.jpg');
        $file->getFilesystem()->shouldBeCalled()->willReturn($filesystem);
        $filesystem->getAdapter()->willReturn($adapter);

        $this->fileAsset($file)->shouldReturn('/TestFolder/File/file.jpg');
        $this->filePath($file)->shouldReturn('/TestFolder/File/file.jpg');
    }

    function it_generate_url_with_file_path_prefix(File $file, Filesystem $filesystem, Local $adapter)
    {
        $file->getKey()->shouldBeCalled()->willReturn('TestFolder/File/file.jpg');
        $file->getFilesystem()->shouldBeCalled()->willReturn($filesystem);
        $filesystem->getAdapter()->willReturn($adapter);

        $this->fileAsset($file, 'uploaded')->shouldReturn('/uploaded/TestFolder/File/file.jpg');
        $this->filePath($file, 'uploaded')->shouldReturn('/uploaded/TestFolder/File/file.jpg');
    }

    function it_generate_url_with_file_path_prefix_that_should_be_trimed(File $file, Filesystem $filesystem, Local $adapter)
    {
        $file->getKey()->shouldBeCalled()->willReturn('TestFolder/File/file.jpg');
        $file->getFilesystem()->shouldBeCalled()->willReturn($filesystem);
        $filesystem->getAdapter()->willReturn($adapter);

        $this->fileAsset($file, '/uploaded/')->shouldReturn('/uploaded/TestFolder/File/file.jpg');
        $this->filePath($file, '/uploaded/')->shouldReturn('/uploaded/TestFolder/File/file.jpg');
    }

    function it_generate_url_for_fsi_file_with_external_adapter(File $file, Filesystem $filesystem, Local $adapter)
    {
        $file->getKey()->shouldBeCalled()->willReturn('TestFolder/File/file.jpg');
        $file->getFilesystem()->shouldBeCalled()->willReturn($filesystem);
        $filesystem->getAdapter()->willReturn($adapter);

        $file->getContent()->shouldBeCalled()->willReturn('file content');

        $this->fileAsset($file)->shouldReturn('/TestFolder/File/file.jpg');
        $this->filePath($file)->shouldReturn('/TestFolder/File/file.jpg');
    }

    function it_generate_url_with_prefix_from_globals(
        Twig_Environment $environment, File $file, Filesystem $filesystem, Local $adapter
    ) {
        $environment->getGlobals()->willReturn(array(
                'fsi_file_prefix' => 'uploaded'
            ));
        $this->initRuntime($environment);

        $file->getKey()->shouldBeCalled()->willReturn('TestFolder/File/file.jpg');
        $file->getFilesystem()->shouldBeCalled()->willReturn($filesystem);
        $filesystem->getAdapter()->willReturn($adapter);

        $this->fileAsset($file)->shouldReturn('/uploaded/TestFolder/File/file.jpg');
        $this->filePath($file)->shouldReturn('/uploaded/TestFolder/File/file.jpg');
    }

    function it_generate_url_with_passed_prefix_even_if_there_is_a_prefix_in_globals(
        Twig_Environment $environment, File $file, Filesystem $filesystem, Local $adapter
    ) {
        $environment->getGlobals()->willReturn(array(
                'fsi_file_prefix' => 'uploaded'
            ));
        $this->initRuntime($environment);

        $file->getKey()->shouldBeCalled()->willReturn('TestFolder/File/file.jpg');
        $file->getFilesystem()->shouldBeCalled()->willReturn($filesystem);
        $filesystem->getAdapter()->willReturn($adapter);

        $this->fileAsset($file, 'my_prefix')->shouldReturn('/my_prefix/TestFolder/File/file.jpg');
        $this->filePath($file, 'my_prefix')->shouldReturn('/my_prefix/TestFolder/File/file.jpg');
    }

    /**
     * @param \FSi\DoctrineExtensions\Uploadable\File $file
     */
    function it_generate_fsi_file_basename($file)
    {
        $file->getName()->shouldBeCalled()->willReturn('TestFolder/File/file.jpg');

        $this->fileBasename($file)->shouldReturn('file.jpg');
    }

    function letgo()
    {
        if (file_exists(__DIR__ . '/tmp')) {
            self::deleteRecursive(__DIR__ . '/tmp');
            rmdir(__DIR__ . '/tmp');
        }
    }

    /**
     * @param string $path
     */
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
