<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\Tests\Fixtures\Entity;

use FSi\DoctrineExtensions\Uploadable\File;

class Article
{
    /**
     * @var \FSi\DoctrineExtensions\Uploadable\File
     */
    protected $file;

    public function getFile()
    {
        return $this->file;
    }

    public function setFile(?File $file)
    {
        $this->file = $file;
    }
}
