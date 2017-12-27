<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DoctrineExtensionsBundle\EventListener;

use FSi\DoctrineExtensions\Translatable\TranslatableListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class TranslatableLocaleListener implements EventSubscriberInterface
{
    /**
     * @var TranslatableListener
     */
    private $translatableListener;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest'
        ];
    }

    public function __construct(TranslatableListener $translatableListener)
    {
        $this->translatableListener = $translatableListener;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->translatableListener->setLocale($event->getRequest()->getLocale());
    }
}
