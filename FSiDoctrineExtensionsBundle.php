<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle;

use FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection\Compiler\GaufretteFilesystemsPass;
use FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection\Compiler\Symfony3ValidatorPass;
use FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection\Compiler\TwigDataGridPass;
use FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection\Compiler\TwigGlobalsPass;
use FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection\FSIDoctrineExtensionsExtension;
use FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection\Compiler\TwigFormPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FSiDoctrineExtensionsBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TwigFormPass());
        $container->addCompilerPass(new Symfony3ValidatorPass());
        $container->addCompilerPass(new GaufretteFilesystemsPass(), PassConfig::TYPE_AFTER_REMOVING);

        if ($container->hasExtension('fsi_data_grid')) {
            $container->addCompilerPass(new TwigDataGridPass());
        }
    }

    /**
     * @return \FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection\FSIDoctrineExtensionsExtension|\Symfony\Component\DependencyInjection\Extension\ExtensionInterface
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new FSIDoctrineExtensionsExtension();
        }

        return $this->extension;
    }
}
