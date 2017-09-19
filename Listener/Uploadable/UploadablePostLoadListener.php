<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class UploadablePostLoadListener implements EventSubscriber
{
    /**
     * @var \FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable\UploadableListener
     */
    private $uploadableListener;

    /**
     * @param \FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable\UploadableListener $uploadableListener
     */
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

    /**
     * After loading the entity load file if any.
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $eventArgs
     */
    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $this->uploadableListener->postLoad($eventArgs);
    }
}
