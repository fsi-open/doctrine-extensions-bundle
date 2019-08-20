<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\Form\Type\FSi;

use FSi\Bundle\DoctrineExtensionsBundle\Form\EventListener\FileSubscriber;
use FSi\Bundle\DoctrineExtensionsBundle\Form\EventListener\RemovableFileSubscriber;
use FSi\Bundle\DoctrineExtensionsBundle\Form\Type\FSi\FileType;
use PhpSpec\ObjectBehavior;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
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
        $this->shouldBeAnInstanceOf(AbstractType::class);
    }

    function it_should_have_valid_name()
    {
        $this->getBlockPrefix()->shouldReturn('fsi_removable_file');
    }

    function it_should_be_child_of_form_type()
    {
        $this->getParent()->shouldReturn($this->isSymfony28() ? FormType::class : 'form');
    }

    function it_should_set_default_options(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'compound' => true,
            'error_bubbling' => false,
            'inherit_data' => true,
            'required' => false,
            'remove_name' => 'remove',
            'remove_type' => $this->isSymfony28() ? CheckboxType::class : 'checkbox',
            'remove_options' => [],
            'file_type' => $this->isSymfony28() ? FileType::class : 'fsi_file',
            'file_options' => [],
        ])->shouldBeCalled();

        $resolver->setAllowedTypes('remove_name', 'string')->shouldBeCalled();
        $resolver->setAllowedTypes('remove_type', 'string')->shouldBeCalled();
        $resolver->setAllowedTypes('remove_options', 'array')->shouldBeCalled();
        $resolver->setAllowedTypes('file_type', 'string')->shouldBeCalled();
        $resolver->setAllowedTypes('file_options', 'array')->shouldBeCalled();

        if ($this->isSymfony27()) {
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
        RemovableFileSubscriber $removableFileSubscriber
    ) {
        $builder->getName()->willReturn('file_field_name');

        $builder->add('file_field_name', 'file_field_type', [
            'label' => false,
            'error_bubbling' => true,
            'some_file_field_option' => 'file_option_value'
        ])->shouldBeCalled();

        $builder->add('remove_field_name', 'remove_field_type', [
            'required' => false,
            'label' => 'fsi_removable_file.remove',
            'mapped' => false,
            'translation_domain' => 'FSiDoctrineExtensionsBundle',
            'some_remove_field_option' => 'remove_option_value'
        ])->shouldBeCalled();

        $builder->get('file_field_name')->willReturn($fileBuilder);

        $fileBuilder->getEventDispatcher()->willReturn($fileEventDispatcher);
        $fileEventDispatcher->getListeners(FormEvents::PRE_SUBMIT)->willReturn([
            [$fileSubscriber]
        ]);
        $fileEventDispatcher->removeSubscriber($fileSubscriber)->shouldBeCalled();

        $builder->addEventSubscriber($removableFileSubscriber)->shouldBeCalled();

        $this->buildForm($builder, [
            'file_type' => 'file_field_type',
            'file_options' => ['some_file_field_option' => 'file_option_value'],
            'remove_name' => 'remove_field_name',
            'remove_type' => 'remove_field_type',
            'remove_options' => ['some_remove_field_option' => 'remove_option_value']
        ]);
    }

    function it_changes_label_attribute_for(
        FormInterface $form,
        FormView $view,
        FormView $fileView
    ) {
        $form->getName()->willReturn('file');

        $view->offsetGet('file')->willReturn($fileView);
        $view->vars = ['label_attr' => []];
        $fileView->vars = ['id' => 'form_file_file'];
        $this->finishView($view, $form, []);

        expect($view->vars['label_attr']['for'])->toBe('form_file_file');
    }

    private function isSymfony27(): bool
    {
        return method_exists(AbstractType::class, 'configureOptions');
    }

    private function isSymfony28(): bool
    {
        return method_exists(AbstractType::class, 'getBlockPrefix');
    }
}
