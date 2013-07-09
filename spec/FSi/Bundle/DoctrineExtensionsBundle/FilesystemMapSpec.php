<?php

namespace spec\FSi\Bundle\DoctrineExtensionsBundle;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @author Norbert Orzechowicz <norbert@fsi.pl>
 */
class FilesystemMapSpec extends ObjectBehavior
{
    /**
     * @param \Knp\Bundle\GaufretteBundle\FilesystemMap $filesystemMap
     * @param \Gaufrette\Filesystem $filesystem
     */
    function let($filesystemMap, $filesystem)
    {
        $filesystemMap->getIterator()->shouldBeCalled()->willReturn(array(
            'filesystem' => $filesystem
        ));

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
