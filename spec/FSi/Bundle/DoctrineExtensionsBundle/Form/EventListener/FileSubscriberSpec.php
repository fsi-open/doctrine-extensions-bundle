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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Test\FormInterface;

class FileSubscriberSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('FSi\Bundle\DoctrineExtensionsBundle\Form\EventListener\FileSubscriber');
    }

    function it_passes_form_data_as_submitted_data_when_there_is_no_submitted_data(FormEvent $event, FormInterface $form)
    {
        $event->getData()->shouldBeCalled()->willReturn(null);

        $event->getForm()->shouldBeCalled()->willReturn($form);

        $form->getData()->shouldBeCalled()->willReturn('fake');

        $event->setData('fake')->shouldBeCalled();

        $this->preSubmit($event);
    }

    function it_does_not_modify_submitted_data(FormEvent $event, FormInterface $form)
    {
        $event->getData()->shouldBeCalled()->willReturn('value');

        $event->getForm()->willReturn($form);

        $form->getData()->shouldNotBeCalled();

        $event->setData(Argument::any())->shouldNotBeCalled();

        $this->preSubmit($event);
    }
}
