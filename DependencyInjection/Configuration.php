<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        if (true === method_exists(TreeBuilder::class, 'getRootNode')) {
            $treeBuilder = new TreeBuilder('fsi_doctrine_extensions');
            $rootNode = $treeBuilder->getRootNode();
        } else {
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('fsi_doctrine_extensions');
        }

        $rootNode
            ->children()
                ->append($this->getVendorNode())
                ->append($this->getFilesystemsNode())
                ->scalarNode('default_locale')->defaultValue('%locale%')->end()
                ->scalarNode('default_key_maker_service')
                    ->defaultValue('fsi_doctrine_extensions.default.key_maker')
                ->end()
                ->scalarNode('default_filesystem_prefix')->defaultValue('uploaded')->end()
                ->scalarNode('default_filesystem_base_url')->defaultValue('/uploaded')->end()
                ->scalarNode('default_filesystem_path')->defaultValue('%kernel.root_dir%/../web/uploaded')->end()
                ->scalarNode('default_filesystem_service')
                    ->defaultValue('fsi_doctrine_extensions.default.filesystem')
                ->end()
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

    private function getFilesystemsNode(): NodeDefinition
    {
        if (true === method_exists(TreeBuilder::class, 'getRootNode')) {
            $treeBuilder = new TreeBuilder('uploadable_filesystems');
            $node = $treeBuilder->getRootNode();
        } else {
            $treeBuilder = new TreeBuilder();
            $node = $treeBuilder->root('uploadable_filesystems');
        }

        $node
            ->useAttributeAsKey('filesystem')
            ->prototype('array')
            ->children()
                ->scalarNode('filesystem')->end()
                ->scalarNode('base_url')->end()
            ->end();

        return $node;
    }

    private function getVendorNode(): NodeDefinition
    {

        if (true === method_exists(TreeBuilder::class, 'getRootNode')) {
            $treeBuilder = new TreeBuilder('orm');
            $node = $treeBuilder->getRootNode();
        } else {
            $treeBuilder = new TreeBuilder();
            $node = $treeBuilder->root('orm');
        }

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
