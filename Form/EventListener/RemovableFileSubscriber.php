<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\Form\EventListener;

use FSi\DoctrineExtensions\Uploadable\Mapping\Annotation\Uploadable;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class RemovableFileSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * @param \Symfony\Component\PropertyAccess\PropertyAccessor $accessor
     */
    public function __construct(PropertyAccessor $accessor)
    {
        $this->propertyAccessor = $accessor;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'preSubmit',
           );
    }

    /**
     * @param \Symfony\Component\Form\FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {

        $data = $event->getData();
        if (empty($data)) {
            $data = $event->getForm()->getData();
            $event->setData($data);
        }

        $entity = $event->getForm()->getViewData();
        if (!empty($entity)) {

            $propertyPath = $event->getForm()->get('file')->getConfig()->getOption('property_path');

            if (!empty($data['delete'])) {
                $this->propertyAccessor->setValue($entity, $propertyPath, null);
            }
            //passed previous data if user doesn't select checkbox
            if (empty($data['file'])) {
                $data['file'] = $this->propertyAccessor->getValue($entity, $propertyPath);
                $event->setData($data);
            }
        }
    }
}
