<?php

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
