<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable\UploadableListener;

class UploadablePostLoadListener implements EventSubscriber
{
    /**
     * @var UploadableListener
     */
    private $uploadableListener;

    public function __construct(UploadableListener $uploadableListener)
    {
        $this->uploadableListener = $uploadableListener;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            'postLoad'
        ];
    }

    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $this->uploadableListener->postLoad($eventArgs);
    }
}
