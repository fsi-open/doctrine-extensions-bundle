<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

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
                $subscriber = sprintf('fsi_doctrine_extensions.listener.%s', $name);

                if ($enabled && $container->hasDefinition($subscriber)) {
                    $attributes = array('connection' => $connection);
                    $definition = $container->getDefinition($subscriber);
                    $definition->addTag('doctrine.event_subscriber', $attributes);
                }
            }
        }
        $this->setUploadableConfiguration($container, $config);
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
}
