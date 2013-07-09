<?php

namespace FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Norbert Orzechowicz <norbert@fsi.pl>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fsi_doctrine_extensions');

        $rootNode
            ->children()
                ->append($this->getVendorNode('orm'))
                ->scalarNode('default_key_maker_service')->defaultValue('fsi_doctrine_extensions.default.key_maker')->end()
                ->scalarNode('default_filesystem_path')->defaultValue('%kernel.root_dir%/../web/uploaded')->end()
                ->scalarNode('default_filesystem_service')->defaultValue('fsi_doctrine_extensions.default.filesystem')->end()
            ->end();

        return $treeBuilder;
    }

    private function getVendorNode($name)
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root($name);

        $node
            ->useAttributeAsKey('id')
            ->prototype('array')
            ->performNoDeepMerging()
                ->children()
                    ->scalarNode('uploadable')->defaultFalse()->end()
                ->end()
            ->end();

        return $node;
    }
}
