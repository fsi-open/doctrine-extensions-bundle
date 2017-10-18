<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DoctrineExtensionsBundle\Resolver;

use FSi\Bundle\DoctrineExtensionsBundle\Exception\Uploadable\InvalidFilesystemException;
use FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable\Filesystem;
use FSi\DoctrineExtensions\Uploadable\File;

class FSiFilePathResolver
{
    /**
     * @param File $file
     * @return string
     */
    public function fileBasename(File $file): string
    {
        return basename($file->getName());
    }

    /**
     * @param File $file
     * @return string
     */
    public function fileUrl(File $file): string
    {
        $filesystem = $file->getFilesystem();
        if (!($filesystem instanceof Filesystem)) {
            throw new InvalidFilesystemException(sprintf(
                'Expected instance of "%s", got "%s" instead',
                Filesystem::class,
                is_object($filesystem) ? get_class($filesystem) : gettype($filesystem)
            ));
        }

        return sprintf('%s%s', $filesystem->getBaseUrl(), $file->getKey());
    }
}
