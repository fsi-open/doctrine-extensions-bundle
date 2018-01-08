<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\EventListener;

use FSi\DoctrineExtensions\Translatable\TranslatableListener;
use PhpSpec\ObjectBehavior;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class TranslatableLocaleListenerSpec extends ObjectBehavior
{
    function let(TranslatableListener $translatableListener)
    {
        $this->beConstructedWith($translatableListener);
    }

    function it_is_event_subscriber()
    {
        $this->shouldBeAnInstanceOf(EventSubscriberInterface::class);
    }

    function it_handles_on_kernel_request_event()
    {
        $this->getSubscribedEvents()->shouldReturn([
            KernelEvents::REQUEST => 'onKernelRequest'
        ]);
    }

    function it_passes_locale_from_request_to_translatable_listener(
        TranslatableListener $translatableListener,
        GetResponseEvent $event,
        Request $request
    ) {
        $event->getRequest()->willReturn($request);
        $request->getLocale()->willReturn('some_locale');
        $translatableListener->setLocale('some_locale')->shouldBeCalled();

        $this->onKernelRequest($event);
    }
}
