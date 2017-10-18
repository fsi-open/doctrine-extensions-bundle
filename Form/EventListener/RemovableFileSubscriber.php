<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

class RemovableFileSubscriber implements EventSubscriberInterface
{
    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * @param PropertyAccessor $accessor
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
        return [
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    /**
     * @param \Symfony\Component\Form\FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        if ($this->isEventFormDataEmpty($event)) {
            return;
        }

        if ($this->shouldFileBeRemoved($event)) {
            $this->removeFile($event);
        } elseif (!$this->isNewFileSubmitted($event)) {
            $this->copyFileFromFormDataToEventData($event);
        }
    }

    /**
     * @param FormEvent $event
     * @return bool
     */
    private function isEventFormDataEmpty(FormEvent $event)
    {
        return null === $event->getForm()->getData();
    }

    /**
     * @param FormEvent $event
     * @return bool
     */
    private function shouldFileBeRemoved(FormEvent $event)
    {
        $submittedData = $event->getData();
        $removeName = $event->getForm()->getConfig()->getOption('remove_name');

        return !empty($submittedData[$removeName]);
    }

    /**
     * @param FormEvent $event
     */
    private function removeFile(FormEvent $event)
    {
        $formData = $event->getForm()->getData();
        $propertyPath = $this->getEventFormPropertyPath($event);
        $this->propertyAccessor->setValue($formData, $propertyPath, null);
    }

    /**
     * @param FormEvent $event
     * @return bool
     */
    private function isNewFileSubmitted(FormEvent $event)
    {
        $submittedData = $event->getData();
        $formName = $event->getForm()->getName();

        return !empty($submittedData[$formName]);
    }

    /**
     * @param FormEvent $event
     */
    private function copyFileFromFormDataToEventData(FormEvent $event)
    {
        $formData = $event->getForm()->getData();
        $formName = $event->getForm()->getName();
        $propertyPath = $this->getEventFormPropertyPath($event);
        $submittedData = $event->getData();
        $submittedData[$formName] = $this->propertyAccessor->getValue($formData, $propertyPath);
        $event->setData($submittedData);
    }

    /**
     * @param FormEvent $event
     * @return PropertyPathInterface
     */
    private function getEventFormPropertyPath(FormEvent $event)
    {
        return $event->getForm()->getPropertyPath();
    }
}
