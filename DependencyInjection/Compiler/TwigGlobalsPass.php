<?php

namespace FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TwigGlobalsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('twig')) {
            return;
        }

        $container->findDefinition('twig')->addMethodCall(
            'addGlobal',
            array(
                'fsi_file_prefix',
                $container->getParameter('fsi_doctrine_extensions.default.filesystem.adapter.prefix')
            )
        );
    }
}
