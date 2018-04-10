<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class Symfony3ValidatorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // This interface is removed in symfony/validator 3.0
        if (!interface_exists('Symfony\Component\Validator\ExecutionContextInterface')) {
            $container->findDefinition('fsi_doctrine_extensions.validator.file')
                ->setClass('FSi\Bundle\DoctrineExtensionsBundle\Validator\Constraints\Symfony3\FileValidator');

            $container->findDefinition('fsi_doctrine_extensions.validator.image')
                ->setClass('FSi\Bundle\DoctrineExtensionsBundle\Validator\Constraints\Symfony3\ImageValidator');
        }
    }
}
