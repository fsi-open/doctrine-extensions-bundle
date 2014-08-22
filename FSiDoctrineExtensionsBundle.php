<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle;

use FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection\Compiler\CustomHydratorPass;
use FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection\Compiler\TwigGlobalsPass;
use FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection\FSIDoctrineExtensionsExtension;
use FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection\Compiler\TwigFormPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FSiDoctrineExtensionsBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TwigFormPass());
        $container->addCompilerPass(new TwigGlobalsPass());
        $container->addCompilerPass(new CustomHydratorPass());
    }

    /**
     * @return null|\FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection\FSIDoctrineExtensionsExtension|\Symfony\Component\DependencyInjection\Extension\ExtensionInterface
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new FSIDoctrineExtensionsExtension();
        }

        return $this->extension;
    }
}
