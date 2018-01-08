<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable;

use FSi\Bundle\DoctrineExtensionsBundle\FilesystemMap;
use FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable\UploadableListener;
use FSi\DoctrineExtensions\Uploadable\FileHandler\GaufretteHandler;
use FSi\DoctrineExtensions\Uploadable\UploadableListener as BaseUploadableLister;
use PhpSpec\ObjectBehavior;

class UploadableListenerSpec extends ObjectBehavior
{
    function let(FilesystemMap $map, GaufretteHandler $handler)
    {
        $map->all()->willReturn([]);
        $this->beConstructedWith($map, $handler);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UploadableListener::class);
    }

    function it_extends_uploadable()
    {
        $this->shouldBeAnInstanceOf(BaseUploadableLister::class);
    }
}
