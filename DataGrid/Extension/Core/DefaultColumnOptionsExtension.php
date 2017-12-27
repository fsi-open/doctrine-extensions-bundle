<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DoctrineExtensionsBundle\DataGrid\Extension\Core;

use FSi\Component\DataGrid\Extension\Core\ColumnTypeExtension\DefaultColumnOptionsExtension as BaseExtension;

class DefaultColumnOptionsExtension extends BaseExtension
{
    public function getExtendedColumnTypes(): array
    {
        return ['fsi_file', 'fsi_image'];
    }
}
