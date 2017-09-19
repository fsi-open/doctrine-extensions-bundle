<?php

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
