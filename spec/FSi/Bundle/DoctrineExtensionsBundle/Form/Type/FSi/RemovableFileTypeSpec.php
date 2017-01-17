<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\Form\Type\FSi;

use FSi\Bundle\DoctrineExtensionsBundle\Form\EventListener\FileSubscriber;
use FSi\Bundle\DoctrineExtensionsBundle\Form\EventListener\RemovableFileSubscriber;
use PhpSpec\ObjectBehavior;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RemovableFileTypeSpec extends ObjectBehavior
{
    function let(RemovableFileSubscriber $removableFileSubscriber)
    {
        $this->beConstructedWith($removableFileSubscriber);
    }

    function it_is_form_type()
    {
        $this->shouldBeAnInstanceOf('Symfony\Component\Form\AbstractType');
    }

    function it_should_have_valid_name()
    {
        $this->getName()->shouldReturn('fsi_removable_file');
    }

    function it_should_be_child_of_form_type()
    {
        $this->getParent()->shouldReturn($this->isSymfony3()
            ? 'Symfony\Component\Form\Extension\Core\Type\FormType'
            : 'form'
        );
    }

    function it_should_set_default_options(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'compound' => true,
            'error_bubbling' => false,
            'inherit_data' => true,
            'required' => false,
            'remove_name' => 'remove',
            'remove_type' => $this->isSymfony3()
                ? 'Symfony\Component\Form\Extension\Core\Type\CheckboxType'
                : 'checkbox',
            'remove_options' => array(),
            'file_type' => $this->isSymfony3()
                ? 'FSi\Bundle\DoctrineExtensionsBundle\Form\Type\FSi\FileType'
                : 'fsi_file',
            'file_options' => array(),
        ))->shouldBeCalled();

        $resolver->setAllowedTypes('remove_name', 'string')->shouldBeCalled();
        $resolver->setAllowedTypes('remove_type', 'string')->shouldBeCalled();
        $resolver->setAllowedTypes('remove_options', 'array')->shouldBeCalled();
        $resolver->setAllowedTypes('file_type', 'string')->shouldBeCalled();
        $resolver->setAllowedTypes('file_options', 'array')->shouldBeCalled();

        if ($this->isSymfony3()) {
            $this->configureOptions($resolver);
        } else {
            $this->setDefaultOptions($resolver);
        }
    }

    function it_should_build_form_remove_original_listener_and_register_own_listener(
        FormBuilderInterface $builder,
        FormBuilderInterface $fileBuilder,
        EventDispatcherInterface $fileEventDispatcher,
        FileSubscriber $fileSubscriber,
        RemovableFileSubscriber $removableFileSubscriber)
    {
        $builder->getName()->willReturn('file_field_name');

        $builder->add('file_field_name', 'file_field_type', array(
            'label' => false,
            'error_bubbling' => true,
            'some_file_field_option' => 'file_option_value'
        ))->shouldBeCalled();

        $builder->add('remove_field_name', 'remove_field_type', array(
            'required' => false,
            'label' => 'fsi_removable_file.remove',
            'mapped' => false,
            'translation_domain' => 'FSiDoctrineExtensionsBundle',
            'some_remove_field_option' => 'remove_option_value'
        ))->shouldBeCalled();

        $builder->get('file_field_name')->willReturn($fileBuilder);

        $fileBuilder->getEventDispatcher()->willReturn($fileEventDispatcher);
        $fileEventDispatcher->getListeners(FormEvents::PRE_SUBMIT)->willReturn(array(
            array($fileSubscriber)
        ));
        $fileEventDispatcher->removeSubscriber($fileSubscriber)->shouldBeCalled();

        $builder->addEventSubscriber($removableFileSubscriber)
            ->shouldBeCalled();

        $this->buildForm($builder, array(
            'file_type' => 'file_field_type',
            'file_options' => array('some_file_field_option' => 'file_option_value'),
            'remove_name' => 'remove_field_name',
            'remove_type' => 'remove_field_type',
            'remove_options' => array('some_remove_field_option' => 'remove_option_value')
        ));
    }

    function it_changes_label_attribute_for(
        FormInterface $form,
        FormView $view,
        FormView $fileView
    ) {
        $form->getName()->willReturn('file');

        $view->offsetGet('file')->willReturn($fileView);
        $view->vars = array('label_attr' => array());

        $fileView->vars = array('id' => 'form_file_file');

        $this->finishView($view, $form, array());

        expect($view->vars['label_attr']['for'])->toBe('form_file_file');
    }

    /**
     * @return bool
     */
    private function isSymfony3()
    {
        return method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix');
    }
}
