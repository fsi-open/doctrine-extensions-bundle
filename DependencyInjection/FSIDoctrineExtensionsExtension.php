<?php

namespace FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * @author Norbert Orzechowicz <norbert@fsi.pl>
 */
class FSIDoctrineExtensionsExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container->getDefinition('fsi_doctrine_extensions.listener.uploadable')->addMethodCall(
            'setDefaultKeymaker',
            array(new Reference($config['default_key_maker_service']))
        );
        $container->setParameter('fsi_doctrine_extensions.default.filesystem.adapter.path', $config['default_filesystem_path']);
        $container->getDefinition('fsi_doctrine_extensions.listener.uploadable')->addMethodCall(
            'setDefaultFilesystem',
            array(new Reference($config['default_filesystem_service']))
        );

        $this->setORMConfiguration($container, $config['orm']);
    }

    /**
     * @param ContainerBuilder $container
     * @param array $config
     */
    protected function setORMConfiguration(ContainerBuilder $container, $config = array())
    {
        foreach ($config as $connection => $subscribers) {
            foreach ($subscribers as $name => $enabled) {
                $subscriber = sprintf('fsi_doctrine_extensions.listener.%s', $name);
                $attributes = array('connection' => $connection);

                if ($enabled && $container->hasDefinition($subscriber)) {
                    $definition = $container->getDefinition($subscriber);
                    $definition->addTag('doctrine.event_subscriber', $attributes);
                }
            }
        }
    }
}
