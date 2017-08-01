<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

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

        if (class_exists('\FSi\Bundle\DataGridBundle\DataGridBundle')) {
            $loader->load('services/datagrid.xml');
        }
        $this->setListenersConfiguration($container, $config);
        $this->setUploadabbleConfigurationParameter($container, $config['uploadable_configuration']);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array $config
     */
    protected function setListenersConfiguration(ContainerBuilder $container, $config = array())
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
                        $attributes = array(
                            'connection' => $connection,
                            'priority' => reset($definitionTag)['priority']
                        );
                        $subscriberDefinition->addTag('doctrine.event_subscriber', $attributes);
                    }
                }
            }
        }
        $this->setUploadableConfiguration($container, $config);
        $this->setTranslatableConfiguration($container, $config);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array $config
     */
    protected function setUploadableConfiguration(ContainerBuilder $container, $config = array())
    {
        $container->getDefinition('fsi_doctrine_extensions.listener.uploadable')->addMethodCall(
            'setDefaultKeymaker',
            array(new Reference($config['default_key_maker_service']))
        );
        $container->setParameter('fsi_doctrine_extensions.default.filesystem.adapter.path', $config['default_filesystem_path']);
        $container->getDefinition('fsi_doctrine_extensions.listener.uploadable')->addMethodCall(
            'setDefaultFilesystem',
            array(new Reference($config['default_filesystem_service']))
        );

        $container->setParameter(
            'fsi_doctrine_extensions.default.filesystem.adapter.prefix',
            $config['default_filesystem_prefix']
        );

        $container->setParameter(
            'fsi_doctrine_extensions.default.filesystem.base_url',
            $config['default_filesystem_base_url']
        );
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array $config
     */
    protected function setUploadabbleConfigurationParameter(ContainerBuilder $container, $config = array())
    {
        $configuration = array();

        foreach ($config as $entity => $entityConfig) {
            $configuration[$entity] = $entityConfig['configuration'];
        }

        $container->setParameter('fsi_doctrine_extensions.listener.uploadable.configuration', $configuration);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array $config
     */
    protected function setTranslatableConfiguration(ContainerBuilder $container, $config = array())
    {
        $container->getDefinition('fsi_doctrine_extensions.listener.translatable')
            ->addMethodCall('setDefaultLocale', array($config['default_locale']));
    }
}
