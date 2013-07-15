<?php

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\Form\Type\Admin\FSi;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FileTypeSpec extends ObjectBehavior
{
    /**
     * @param \FSi\Bundle\AdminBundle\Structure\GroupManager $manager
     * @param \Symfony\Bundle\FrameworkBundle\Routing\Router $router
     */
    function let($manager, $router)
    {
        $this->beConstructedWith($manager, $router);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('FSi\Bundle\DoctrineExtensionsBundle\Form\Type\Admin\FSi\FileType');
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
}
