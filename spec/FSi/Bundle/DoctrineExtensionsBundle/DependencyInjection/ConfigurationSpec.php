<?php

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @author Norbert Orzechowicz <norbert@fsi.pl>
 */
class ConfigurationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('\FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection\Configuration');
    }

    function it_should_return_tree()
    {
        $this->getConfigTreeBuilder()->shouldReturnAnInstanceOf('\Symfony\Component\Config\Definition\Builder\TreeBuilder');
    }
}
