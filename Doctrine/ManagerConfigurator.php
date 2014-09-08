<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\Doctrine;

use Doctrine\Bundle\DoctrineBundle\ManagerConfigurator as BaseManagerConfigurator;
use Doctrine\ORM\EntityManager;
use FSi\DoctrineExtensions\ORM\Query;

class ManagerConfigurator extends BaseManagerConfigurator
{
    public function configure(EntityManager $entityManager)
    {
        parent::configure($entityManager);

        $entityManager->getConfiguration()->addCustomHydrationMode(
            Query::HYDRATE_OBJECT,
            'FSi\DoctrineExtensions\ORM\Hydration\ObjectHydrator'
        );
    }
}
