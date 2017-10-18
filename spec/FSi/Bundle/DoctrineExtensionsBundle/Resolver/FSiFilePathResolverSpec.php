<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\Resolver;

use FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable\Filesystem;
use FSi\DoctrineExtensions\Uploadable\File;
use PhpSpec\ObjectBehavior;

class FSiFilePathResolverSpec extends ObjectBehavior
{
    function let(File $file, Filesystem $filesystem)
    {
        $file->getFilesystem()->willReturn($filesystem);
    }

    function it_generates_file_url_with_prefix(
        File $file,
        Filesystem $filesystem
    ) {
        $file->getKey()->willReturn('TestFolder/File/file1@2.jpg');
        $filesystem->getBaseUrl()->willReturn('/uploaded/');

        $this->fileUrl($file)->shouldReturn('/uploaded/TestFolder/File/file1@2.jpg');
    }

    function it_generates_file_url_for_domain(
        File $file,
        Filesystem $filesystem
    ) {
        $file->getKey()->willReturn('TestFolder/File/file name.jpg');
        $filesystem->getBaseUrl()->willReturn("http://domain.com/basepath/");

        $this->fileUrl($file)->shouldReturn('http://domain.com/basepath/TestFolder/File/file name.jpg');
    }

    function it_generates_fsi_file_basename(File $file)
    {
        $file->getName()->willReturn('TestFolder/File/file.jpg');

        $this->fileBasename($file)->shouldReturn('file.jpg');
    }
}
