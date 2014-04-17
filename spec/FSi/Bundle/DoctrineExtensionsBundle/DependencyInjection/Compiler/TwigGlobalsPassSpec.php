<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class TwigGlobalsPassSpec extends ObjectBehavior
{
    function let(ContainerBuilder $container, Definition $def)
    {
        $container->hasDefinition('twig')->willReturn(true);
        $container->findDefinition('twig')->willReturn($def);
    }

    function it_adds_globals(ContainerBuilder $container, Definition $def)
    {
        $container->getParameter('fsi_doctrine_extensions.default.filesystem.adapter.prefix')->willReturn('test');
        $def->addMethodCall('addGlobal', array('fsi_file_prefix', 'test'))->shouldBeCalled();

        $this->process($container);
    }
}
