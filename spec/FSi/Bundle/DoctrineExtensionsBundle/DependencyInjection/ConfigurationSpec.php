<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection;

use FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection\Configuration;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class ConfigurationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Configuration::class);
    }

    function it_should_return_tree()
    {
        $this->getConfigTreeBuilder()->shouldReturnAnInstanceOf(TreeBuilder::class);
    }
}
