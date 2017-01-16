<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Piotr Szymaszek <piotr.szymaszek@fsi.pl>
 */
class AliceCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $loader = $container->findDefinition('hautelook_alice.alice.fixtures.loader');
        $populatorsIds = $container->findTaggedServiceIds('fsi_doctrine_extensions.alice.populator');
        foreach (array_keys($populatorsIds) as $populatorId) {
            $loader->addMethodCall('addPopulator', [new Reference($populatorId)]);
        }
    }
}
