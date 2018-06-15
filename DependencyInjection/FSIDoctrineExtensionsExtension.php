<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use FSi\Bundle\DataGridBundle\DataGridBundle;

class FSIDoctrineExtensionsExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if (class_exists(DataGridBundle::class)) {
            $loader->load('services/datagrid.xml');
        }
        $this->setListenersConfiguration($container, $config);
        $this->setUploadabbleConfigurationParameter($container, $config['uploadable_configuration']);
    }

    protected function setListenersConfiguration(ContainerBuilder $container, array $config): void
    {
        foreach ($config['orm'] as $connection => $subscribers) {
            foreach ($subscribers as $name => $enabled) {
                $tag = sprintf('fsi_doctrine_extensions.listener.%s', $name);

                if ($enabled) {
                    $listenersIds = $container->findTaggedServiceIds($tag);
                    if (empty($listenersIds)) {
                        continue;
                    }

                    foreach ($listenersIds as $listenerId => $tags) {
                        $subscriberDefinition = $container->getDefinition($listenerId);
                        $definitionTag = $subscriberDefinition->getTag($tag);
                        $attributes = [
                            'connection' => $connection,
                            'priority' => reset($definitionTag)['priority']
                        ];
                        $subscriberDefinition->addTag('doctrine.event_subscriber', $attributes);
                    }
                }
            }
        }
        $this->setUploadableConfiguration($container, $config);
        $this->setTranslatableConfiguration($container, $config);
    }

    protected function setUploadableConfiguration(ContainerBuilder $container, array $config): void
    {
        $container->getDefinition('fsi_doctrine_extensions.listener.uploadable')->addMethodCall(
            'setDefaultKeymaker',
            [new Reference($config['default_key_maker_service'])]
        );
        $container->setParameter('fsi_doctrine_extensions.default.filesystem.adapter.path', $config['default_filesystem_path']);
        $container->getDefinition('fsi_doctrine_extensions.listener.uploadable')->addMethodCall(
            'setDefaultFilesystem',
            [new Reference($config['default_filesystem_service'])]
        );

        $container->setParameter(
            'fsi_doctrine_extensions.default.filesystem.base_url',
            $config['default_filesystem_base_url']
        );

        $filesystemMap = [];
        foreach ($config['uploadable_filesystems'] as $filesystem => $fileSystemConfig) {
            $filesystemMap[$filesystem] = $fileSystemConfig;
        }

        $container->getDefinition('fsi_doctrine_extensions.gaufrette.filesystem_map')
            ->replaceArgument(2, $filesystemMap);
    }

    protected function setUploadabbleConfigurationParameter(ContainerBuilder $container, array $config): void
    {
        $configuration = [];

        foreach ($config as $entity => $entityConfig) {
            $configuration[$entity] = $entityConfig['configuration'];
        }

        $uploadableListenerDefinition = $container->getDefinition('fsi_doctrine_extensions.listener.uploadable');
        $uploadableListenerDefinition->replaceArgument(2, $configuration);
    }

    protected function setTranslatableConfiguration(ContainerBuilder $container, array $config): void
    {
        $container->getDefinition('fsi_doctrine_extensions.listener.translatable')
            ->addMethodCall('setDefaultLocale', [$config['default_locale']]);
    }
}
