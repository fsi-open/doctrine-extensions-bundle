<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

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
