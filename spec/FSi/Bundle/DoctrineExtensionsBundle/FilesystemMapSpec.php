<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\FSi\Bundle\DoctrineExtensionsBundle;

use FSi\Bundle\DoctrineExtensionsBundle\FilesystemMap;
use Gaufrette\Filesystem;
use Gaufrette\FilesystemMap as GaufretteMap;
use Knp\Bundle\GaufretteBundle\FilesystemMap as KnpFilesystemMap;
use PhpSpec\ObjectBehavior;

class FilesystemMapSpec extends ObjectBehavior
{
    function let(KnpFilesystemMap $filesystemMap, Filesystem $filesystem)
    {
        $filesystemMap->getIterator()->shouldBeCalled()->willReturn([
            'filesystem' => $filesystem
        ]);

        $this->beConstructedWith($filesystemMap);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FilesystemMap::class);
    }

    function it_should_be_form_extension()
    {
        $this->shouldHaveType(GaufretteMap::class);
    }

    function it_have_all_filesystems_from_bundle_filesystem_map()
    {
        $this->has('filesystem')->shouldReturn(true);
    }
}
