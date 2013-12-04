<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
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
                ->arrayNode('uploadable_configuration')
                    ->useAttributeAsKey('class')
                    ->prototype('array')
                    ->children()
                        ->scalarNode('class')->end()
                        ->arrayNode('configuration')
                        ->useAttributeAsKey('property')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('property')->end()
                                ->scalarNode('filesystem')->end()
                                ->scalarNode('keymaker')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }

    /**
     * @param string $name
     */
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
                    ->scalarNode('translatable')->defaultFalse()->end()
                ->end()
            ->end();

        return $node;
    }
}
