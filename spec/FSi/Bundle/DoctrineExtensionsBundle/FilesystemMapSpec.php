<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\FSi\Bundle\DoctrineExtensionsBundle;

use Gaufrette\Filesystem;
use Knp\Bundle\GaufretteBundle\FilesystemMap;
use PhpSpec\ObjectBehavior;

class FilesystemMapSpec extends ObjectBehavior
{
    function let(FilesystemMap $filesystemMap, Filesystem $filesystem)
    {
        $filesystemMap->getIterator()->shouldBeCalled()->willReturn([
            'filesystem' => $filesystem
        ]);

        $this->beConstructedWith($filesystemMap);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('FSi\Bundle\DoctrineExtensionsBundle\FilesystemMap');
    }

    function it_should_be_form_extension()
    {
        $this->shouldHaveType('Gaufrette\FilesystemMap');
    }

    function it_have_all_filesystems_from_bundle_filesystem_map()
    {
        $this->has('filesystem')->shouldReturn(true);
    }
}
