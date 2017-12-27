<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class TwigDataGridPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('datagrid.twig.themes')) {
            return;
        }

        $container->setParameter('datagrid.twig.themes', array_merge(
            $container->getParameter('datagrid.twig.themes'),
            ['@FSiDoctrineExtensions/DataGrid/datagrid.html.twig']
        ));
    }
}
