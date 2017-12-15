<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\DataGrid\ColumnType;

use FSi\Component\DataGrid\Column\CellViewInterface;
use FSi\Component\DataGrid\Column\ColumnAbstractType;

class FSiImage extends ColumnAbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return 'fsi_image';
    }

    /**
     * {@inheritdoc}
     */
    public function filterValue($value)
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function buildCellView(CellViewInterface $view): void
    {
        $view->setAttribute('width', $this->getOption('width'));
    }

    /**
     * {@inheritdoc}
     */
    public function initOptions(): void
    {
        $this->getOptionsResolver()
            ->setRequired(['width'])
            ->setAllowedTypes('width', 'integer');
    }
}
