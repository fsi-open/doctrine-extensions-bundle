<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\Form\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Test\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class RemovableFileSubscriberSpec extends ObjectBehavior
{
    function let(PropertyAccessor $accessor)
    {
        $this->beConstructedWith($accessor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('FSi\Bundle\DoctrineExtensionsBundle\Form\EventListener\RemovableFileSubscriber');
    }

    function it_passes_form_data_as_submitted_data_when_there_is_no_submitted_data(
        FormEvent $event,
        FormInterface $form,
        FormConfigInterface $config,
        PropertyAccessor $accessor
    ) {

        $event->getData()->shouldBeCalled()->willReturn(null);

        //set data
        $event->getForm()->shouldBeCalled()->willReturn($form);
        $form->getData()->shouldBeCalled()->willReturn(array('file'=> null));
        $event->getForm()->willReturn($form);
        $event->setData(array('file'=> null))->shouldBeCalled();

        //get entity
        $event->getForm()->willReturn($form);
        $form->getViewData()->willReturn(array('file' => 'value'));

        //get property path
        $event->getForm()->willReturn($form);
        $form->get('file')->willReturn($form);
        $form->getConfig()->willReturn($config);
        $config->getOption('property_path')->willReturn('[file]');

        $accessor->setValue(Argument::any(), Argument::any(), null)->shouldNotBeCalled();

        //passed previous data
        $accessor->getValue(array('file'=>'value'), '[file]')->shouldBeCalled();
        $event->setData(array('file' => 'value'))->shouldNotBeCalled();

        $this->preSubmit($event);
    }

    function it_does_not_modify_submitted_data(
        FormEvent $event,
        FormInterface $form,
        FormConfigInterface $config,
        PropertyAccessor $accessor
    ) {
        $event->getData()->shouldBeCalled()->willReturn(array('file'=>'submittedFile'));

        //get entity
        $event->getForm()->willReturn($form);
        $form->getViewData()->willReturn(array('file' => 'value'));

        //get property path
        $event->getForm()->willReturn($form);
        $form->get('file')->willReturn($form);
        $form->getConfig()->willReturn($config);
        $config->getOption('property_path')->willReturn('[file]');

//        $accessor->setValue(Argument::any(), Argument::any(), null)->shouldNotBeCalled();

//        $event->setData(array('file' => 'submittedFile'))->shouldBeCalled();

        $this->preSubmit($event);
    }

    function it_removes_old_file(
        FormEvent $event,
        FormInterface $form,
        FormConfigInterface $config,
        PropertyAccessor $accessor
    ) {
        $event->getData()->shouldBeCalled()->willReturn(array('delete' => '1', 'file' => 'value'));

        //get entity
        $event->getForm()->willReturn($form);
        $form->getViewData()->willReturn(array('file' => 'value'));

        //get property path
        $event->getForm()->willReturn($form);
        $form->get('file')->willReturn($form);
        $form->getConfig()->willReturn($config);
        $config->getOption('property_path')->willReturn('[file]');

        $accessor->setValue(Argument::any(), Argument::any(), null)->shouldBeCalled();

        $this->preSubmit($event);
    }

    function it_does_not_modify_data_if_object_data_is_empty(
        FormEvent $event,
        FormInterface $form,
        FormConfigInterface $config
    ) {
        $event->getData()->shouldBeCalled()->willReturn(null);

        $event->getForm()->shouldBeCalled()->willReturn($form);
        $form->getData()->shouldBeCalled()->willReturn(null);
        $event->setData(null)->shouldBeCalled();

        $event->getForm()->willReturn($form);
        $form->getViewData()->willReturn(null);

        $form->get('file')->shouldNotBeCalled();
        $form->getConfig()->shouldNotBeCalled();
        $config->getOption('property_path')->shouldNotBeCalled();

        $event->setData(array('file' => 'value'))->shouldNotBeCalled();

        $this->preSubmit($event);
    }

}
