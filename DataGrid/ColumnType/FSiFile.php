<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\DataGrid\ColumnType;

use FSi\Component\DataGrid\Column\ColumnAbstractType;

class FSiFile extends ColumnAbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return 'fsi_file';
    }

    /**
     * {@inheritdoc}
     */
    public function filterValue($value)
    {
        return $value;
    }
}
