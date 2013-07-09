<?php

/**
 * (c) Fabryka Stron Internetowych sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle;

use Gaufrette\FilesystemMap as GaufretteFilesystemMap;
use Knp\Bundle\GaufretteBundle\FilesystemMap as GaufretteBundleFilesystemMap;

/**
 * @author Norbert Orzechowicz <norbert@fsi.pl>
 */
class FilesystemMap extends GaufretteFilesystemMap
{
    public function __construct(GaufretteBundleFilesystemMap $filesystemMap)
    {
        $map = $filesystemMap->getIterator();

        foreach ($map as $domain => $filesystem) {
            $this->set($domain, $filesystem);
        }
    }
}
