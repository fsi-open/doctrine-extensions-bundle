<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\DataGrid\ColumnType;

use FSi\Component\DataGrid\Column\ColumnAbstractType;
use FSi\DoctrineExtensions\Uploadable\File;

class FSiFile extends ColumnAbstractType
{
    /**
     * Get column type identity.
     *
     * @return string
     */
    public function getId()
    {
        return 'fsi_file';
    }

    /**
     * Filter value before passing it to view.
     *
     * @param mixed $value
     */
    public function filterValue($value)
    {
        return $value;
    }
}
