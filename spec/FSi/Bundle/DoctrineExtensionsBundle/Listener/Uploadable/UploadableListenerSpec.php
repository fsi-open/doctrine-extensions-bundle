<?php

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable;

use FSi\Bundle\DoctrineExtensionsBundle\FilesystemMap;
use FSi\DoctrineExtensions\Uploadable\FileHandler\GaufretteHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UploadableListenerSpec extends ObjectBehavior
{
    function let(FilesystemMap $map, GaufretteHandler $handler)
    {
        $map->all()->willReturn(array());
        $this->beConstructedWith($map, $handler);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable\UploadableListener');
    }

    function it_extends_uploadable()
    {
        $this->shouldBeAnInstanceOf('FSi\DoctrineExtensions\Uploadable\UploadableListener');
    }
}
