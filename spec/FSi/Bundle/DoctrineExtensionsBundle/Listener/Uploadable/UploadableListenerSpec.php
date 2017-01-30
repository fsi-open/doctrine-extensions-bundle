<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable;

use FSi\Bundle\DoctrineExtensionsBundle\FilesystemMap;
use FSi\DoctrineExtensions\Uploadable\FileHandler\GaufretteHandler;
use PhpSpec\ObjectBehavior;

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
