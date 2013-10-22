<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle;

use FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection\FSIDoctrineExtensionsExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FSiDoctrineExtensionsBundle extends Bundle
{
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
