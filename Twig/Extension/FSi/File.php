<?php

/**
 * (c) Fabryka Stron Internetowych sp. z o.o <info@fsi.pl>
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
        return array(
           new \Twig_SimpleFunction('is_fsi_file', array($this, 'isFSiFile'))
        );
    }

    /**
     * @param $value
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