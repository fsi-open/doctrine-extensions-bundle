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

class CustomHydratorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $config = current($container->getExtensionConfig('fsi_doctrine_extensions'));
        if (empty($config['orm'])) {
            return;
        }

        foreach ($config['orm'] as $managerName => $listeners) {
            if (!empty($listeners['translatable'])) {
                $configurator = $container->findDefinition('doctrine.orm.manager_configurator.abstract');

                $configurator->setClass('%fsi_doctrine_extensions.orm.manager_configurator.class%');
            }
        }
    }
}
