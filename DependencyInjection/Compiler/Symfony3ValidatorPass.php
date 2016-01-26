<?php

namespace FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

final class Symfony3ValidatorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (version_compare(Kernel::VERSION, '3.0.0', '>=')) {
            $container->findDefinition('fsi_doctrine_extensions.validator.file')
                ->setClass('FSi\Bundle\DoctrineExtensionsBundle\Validator\Constraints\Symfony3\FileValidator');

            $container->findDefinition('fsi_doctrine_extensions.validator.image')
                ->setClass('FSi\Bundle\DoctrineExtensionsBundle\Validator\Constraints\Symfony3\ImageValidator');
        }
    }
}