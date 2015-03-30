<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\Resolver;

use FSi\DoctrineExtensions\Uploadable\File;
use Gaufrette\Adapter\Local;
use Gaufrette\Adapter\Cache;
use Gaufrette\Filesystem;
use PhpSpec\ObjectBehavior;
use FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable\Filesystem as UploadableFilesystem;
use Twig_Environment;

class FSiFilePathResolverSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(__DIR__ . '/tmp', 'uploaded');
    }

    function it_generate_path_for_fsi_file_with_local_adapter(File $file, Filesystem $filesystem, Local $adapter)
    {
        $file->getKey()->willReturn('TestFolder/File/file name.jpg');
        $file->getFilesystem()->willReturn($filesystem);
        $filesystem->getAdapter()->willReturn($adapter);

        $this->filePath($file)->shouldReturn('/uploaded/TestFolder/File/file name.jpg');
    }

    function it_generate_path_for_fsi_file_with_cache_adapter(File $file, Filesystem $filesystem, Local $adapter)
    {
        $file->getKey()->willReturn('/TestFolder/File/file&name.jpg');
        $file->getFilesystem()->willReturn($filesystem);
        $filesystem->getAdapter()->willReturn($adapter);

        $this->filePath($file)->shouldReturn('/uploaded/TestFolder/File/file&name.jpg');
    }

    function it_generate_path_with_file_path_prefix(File $file, Filesystem $filesystem, Cache $adapter)
    {
        $file->getKey()->willReturn('/TestFolder/File/file:name.jpg');
        $file->getFilesystem()->willReturn($filesystem);
        $filesystem->getAdapter()->willReturn($adapter);

        $this->filePath($file, 'uploaded')->shouldReturn('/uploaded/TestFolder/File/file:name.jpg');
    }

    function it_generate_path_with_file_path_prefix_that_should_be_trimed(
        File $file, Filesystem $filesystem, Local $adapter
    ) {
        $file->getKey()->willReturn('TestFolder/File/file<>name.jpg');
        $file->getFilesystem()->willReturn($filesystem);
        $filesystem->getAdapter()->willReturn($adapter);

        $this->filePath($file, '/uploaded/')->shouldReturn('/uploaded/TestFolder/File/file<>name.jpg');
    }

    function it_generate_path_for_fsi_file_with_external_adapter(
        File $file, Filesystem $filesystem, Local $adapter
    ) {
        $file->getKey()->willReturn('Test Folder/File/nazwy plików.jpg');
        $file->getFilesystem()->willReturn($filesystem);
        $filesystem->getAdapter()->willReturn($adapter);

        $file->getContent()->willReturn('file content');

        $this->filePath($file)->shouldReturn('/uploaded/Test Folder/File/nazwy plików.jpg');
    }

    function it_generate_path_with_prefix_from_globals(
        Twig_Environment $environment, File $file, Filesystem $filesystem, Local $adapter
    ) {
        $environment->getGlobals()->willReturn(array('fsi_file_prefix' => 'uploaded'));
        $environment->initRuntime();

        $file->getKey()->willReturn('TestFolder/File/file%name.jpg');
        $file->getFilesystem()->willReturn($filesystem);
        $filesystem->getAdapter()->willReturn($adapter);

        $this->filePath($file)->shouldReturn('/uploaded/TestFolder/File/file%name.jpg');
    }

    function it_generate_path_with_passed_prefix_even_if_there_is_a_prefix_in_globals(
        Twig_Environment $environment, File $file, Filesystem $filesystem, Local $adapter
    ) {
        $environment->getGlobals()->willReturn(array(
                'fsi_file_prefix' => 'uploaded'
            ));
        $environment->initRuntime();

        $file->getKey()->willReturn('TestFolder/File/file1@2.jpg');
        $file->getFilesystem()->willReturn($filesystem);
        $filesystem->getAdapter()->willReturn($adapter);


        $this->filePath($file, 'my_prefix')->shouldReturn('/my_prefix/TestFolder/File/file1@2.jpg');
    }

    function it_generate_url_for_fsi_file_with_base_url_set(
        File $file, UploadableFilesystem $filesystem, Local $adapter
    ) {
        $file->getKey()->willReturn('TestFolder/File/file name.jpg');
        $file->getFilesystem()->willReturn($filesystem);
        $filesystem->getBaseUrl()->willReturn("http://domain.com/basepath/");

        $this->fileUrl($file)->shouldReturn('http://domain.com/basepath/TestFolder/File/file name.jpg');
    }

    /**
     * @param \FSi\DoctrineExtensions\Uploadable\File $file
     */
    function it_generate_fsi_file_basename($file)
    {
        $file->getName()->willReturn('TestFolder/File/file.jpg');

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
