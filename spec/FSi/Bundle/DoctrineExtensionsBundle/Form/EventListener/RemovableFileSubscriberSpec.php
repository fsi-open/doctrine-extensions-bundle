<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\Form\EventListener;

use FSi\DoctrineExtensions\Uploadable\File;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Test\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class RemovableFileSubscriberSpec extends ObjectBehavior
{
    function let(PropertyAccessor $propertyAccessor)
    {
        $this->beConstructedWith($propertyAccessor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('FSi\Bundle\DoctrineExtensionsBundle\Form\EventListener\RemovableFileSubscriber');
    }

    function it_passes_form_data_as_submitted_data_when_there_is_no_submitted_data(
        FormEvent $event,
        FormInterface $form,
        FormConfigInterface $formConfig,
        File $file,
        PropertyAccessor $propertyAccessor
    ) {
        $formData = new \stdClass();
        $event->getData()->willReturn(array('file_field_name' => null));
        $event->getForm()->willReturn($form);
        $form->getName()->willReturn('file_field_name');
        $form->getData()->willReturn($formData);
        $form->getPropertyPath()->willReturn('file_field_name');
        $form->getConfig()->willReturn($formConfig);
        $formConfig->getOption('remove_name')->willReturn('remove_field_name');
        $propertyAccessor->getValue($formData, 'file_field_name')->willReturn($file);

        $event->setData(array('file_field_name' => $file))->shouldBeCalled();

        $this->preSubmit($event);
    }

    function it_removes_file_from_form_data(
        FormEvent $event,
        FormInterface $form,
        FormConfigInterface $formConfig,
        PropertyAccessor $propertyAccessor
    ) {
        $formData = new \stdClass();
        $event->getData()->willReturn(array('remove_field_name' => true));
        $event->getForm()->willReturn($form);
        $form->getName()->willReturn('file_field_name');
        $form->getData()->willReturn($formData);
        $form->getPropertyPath()->willReturn('file_field_name');
        $form->getConfig()->willReturn($formConfig);
        $formConfig->getOption('remove_name')->willReturn('remove_field_name');

        $propertyAccessor->setValue($formData, 'file_field_name', null)->shouldBeCalled();

        $this->preSubmit($event);
    }

    function it_does_nothing_if_form_data_is_empty(
        FormEvent $event,
        FormInterface $form,
        PropertyAccessor $propertyAccessor
    ) {
        $event->getForm()->willReturn($form);
        $form->getData()->willReturn(null);

        $event->setData(Argument::any())->shouldNotBeCalled();
        $propertyAccessor->setValue(Argument::any(), Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->preSubmit($event);
    }
}
