<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\Twig\Extension\FSi;

use FSi\DoctrineExtensions\Uploadable\File as FSiFile;

class File extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'fsi_file';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
           new \Twig_SimpleFunction('is_fsi_file', [$this, 'isFSiFile'])
        ];
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function isFSiFile($value)
    {
        if (!is_object($value)) {
            return false;
        }

        return $value instanceof FSiFile;
    }
}
