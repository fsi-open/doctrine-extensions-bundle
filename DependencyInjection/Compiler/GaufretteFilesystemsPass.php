<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection\Compiler;

use FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class GaufretteFilesystemsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, $container->getExtensionConfig('fsi_doctrine_extensions'));

        foreach ($config['uploadable_filesystems'] as $filesystem => $filesystemConfig) {
            $filesystemService = $container->findDefinition(sprintf('gaufrette.%s_filesystem', $filesystem));
            $filesystemService->addMethodCall('setBaseUrl', [$filesystemConfig['base_url']]);
        }
    }
}
