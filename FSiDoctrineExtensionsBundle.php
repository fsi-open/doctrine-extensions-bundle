<?php

namespace FSi\Bundle\DoctrineExtensionsBundle;

use FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection\FSIDoctrineExtensionsExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Norbert Orzechowicz <norbert@fsi.pl>
 */
class FSiDoctrineExtensionsBundle extends Bundle
{
    /**
     * @return FSIDoctrineExtensionsExtension|null|\Symfony\Component\DependencyInjection\Extension\ExtensionInterface
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new FSIDoctrineExtensionsExtension();
        }

        return $this->extension;
    }
}
