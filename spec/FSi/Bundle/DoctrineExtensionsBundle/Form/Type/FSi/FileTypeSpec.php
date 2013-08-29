<?php

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\Form\Type\FSi;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilderInterface;

class FileTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('FSi\Bundle\DoctrineExtensionsBundle\Form\Type\FSi\FileType');
    }

    function it_should_have_valid_name()
    {
        $this->getName()->shouldReturn('fsi_file');
    }

    function it_should_be_child_of_form_type()
    {
        $this->getParent()->shouldReturn('file');
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    function it_should_set_default_options($resolver)
    {
        $resolver->setDefaults(array(
            'required' => false,
            'data_class' => 'FSi\DoctrineExtensions\Uploadable\File'
        ))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     */
    function it_should_register_listener(FormBuilderInterface $builder)
    {
        $builder->addEventSubscriber(Argument::type('FSi\Bundle\DoctrineExtensionsBundle\Form\EventListener\FileSubscriber'))
            ->shouldBeCalled();

        $this->buildForm($builder, array());
    }
}
