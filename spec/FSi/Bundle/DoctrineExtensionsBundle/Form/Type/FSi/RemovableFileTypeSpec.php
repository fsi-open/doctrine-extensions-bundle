<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\Form\Type\FSi;

use FSi\Bundle\DoctrineExtensionsBundle\Form\EventListener\RemovableFileSubscriber;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class RemovableFileTypeSpec extends ObjectBehavior
{
    function let(RemovableFileSubscriber $fileSubscriber)
    {
        $this->beConstructedWith($fileSubscriber);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('FSi\Bundle\DoctrineExtensionsBundle\Form\Type\FSi\RemovableFileType');
    }

    function it_should_have_valid_name()
    {
        $this->getName()->shouldReturn('fsi_removable_file');
    }

    function it_should_be_child_of_form_type()
    {
        $this->getParent()->shouldReturn('form');
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    function it_should_set_default_options($resolver)
    {
        $resolver->setDefaults(array(
            'compound' => true,
            'inherit_data' => true,
            'delete_label' => 'fsi_removable_file.delete'
        ))->shouldBeCalled();

        $resolver->setRequired(array(
            'property_path'
        ))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     */
    function it_should_register_listener(
        FormBuilderInterface $builder,
        RemovableFileSubscriber $fileSubscriber)
    {
        $builder->add('file', 'file', array(
            'mapped' => true,
            'required' => true,
            'data_class' => 'FSi\DoctrineExtensions\Uploadable\File',
            'property_path' => 'field',
            'label' => false
        ))->shouldBeCalled();

        $builder->add('delete', 'checkbox', array(
                'compound' => false,
                'mapped' => false,
                'required' => false,
                'label' => 'fsi_removable_file.delete'
            )
        )->shouldBeCalled();

        $builder->addEventSubscriber($fileSubscriber)
            ->shouldBeCalled();

        $this->buildForm($builder, array(
            'property_path' => 'field',
            'required' => true,
            'delete_label' => 'fsi_removable_file.delete'
        ));
    }
}
