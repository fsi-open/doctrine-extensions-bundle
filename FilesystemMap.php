<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DoctrineExtensionsBundle;

use Gaufrette\FilesystemMap as GaufretteFilesystemMap;
use Knp\Bundle\GaufretteBundle\FilesystemMap as GaufretteBundleFilesystemMap;
use FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable\Filesystem as FSiFilesystem;

class FilesystemMap extends GaufretteFilesystemMap
{
    public function __construct(GaufretteBundleFilesystemMap $filesystemMap, array $uploadableFilesystems)
    {
        $map = $filesystemMap->getIterator();

        foreach ($map as $domain => $filesystem) {
            if ($filesystem instanceof FSiFilesystem && isset($uploadableFilesystems[$domain]['base_url'])) {
                $filesystem->setBaseUrl($uploadableFilesystems[$domain]['base_url']);
            }

            $this->set($domain, $filesystem);
        }
    }
}
