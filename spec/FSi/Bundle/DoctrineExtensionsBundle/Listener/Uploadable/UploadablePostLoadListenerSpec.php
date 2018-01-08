<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable;

use Doctrine\ORM\Event\LifecycleEventArgs;
use FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable\UploadableListener;
use PhpSpec\ObjectBehavior;

class UploadablePostLoadListenerSpec extends ObjectBehavior
{
    function let(UploadableListener $uploadableListener)
    {
        $this->beConstructedWith($uploadableListener);
    }

    function it_handles_post_load_event()
    {
        $this->getSubscribedEvents()->shouldReturn(['postLoad']);
    }

    function it_delegates_post_load_event(UploadableListener $uploadableListener, LifecycleEventArgs $eventArgs)
    {
        $uploadableListener->postLoad($eventArgs)->shouldBeCalled();

        $this->postLoad($eventArgs);
    }
}
