<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\Form\Type\FSi;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageTypeSpec extends ObjectBehavior
{
    function it_should_have_valid_name()
    {
        $this->getName()->shouldReturn('fsi_image');
    }

    function it_should_be_child_of_fsi_file_type()
    {
        $this->getParent()->shouldReturn('fsi_file');
    }


    function it_should_set_default_options(OptionsResolver $resolver)
    {
        $resolver->setDefaults(Argument::allOf(
            Argument::withEntry(
                'constraints',
                Argument::withEntry(0, Argument::type('\FSi\Bundle\DoctrineExtensionsBundle\Validator\Constraints\Image'))
            )
        ))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }
}
