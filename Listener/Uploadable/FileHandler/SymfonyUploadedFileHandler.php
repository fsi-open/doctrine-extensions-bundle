<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable\FileHandler;

use FSi\DoctrineExtensions\Uploadable\Exception\RuntimeException;
use FSi\DoctrineExtensions\Uploadable\FileHandler\AbstractHandler;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use FSi\Bundle\DoctrineExtensionsBundle\Exception\Uploadable\InvalidFileException;

class SymfonyUploadedFileHandler extends AbstractHandler
{
    public function supports($file): bool
    {
        return $file instanceof UploadedFile;
    }

    public function getName($file): string
    {
        return $file->getClientOriginalName();
    }

    public function getContent($file): string
    {
        if (!$this->supports($file)) {
            throw $this->generateNotSupportedException($file);
        }

        if (!$file->isValid()) {
            throw new InvalidFileException(
                sprintf('File isn\'t uploaded properly! Code of error was "%s".', $file->getError())
            );
        }

        $level = error_reporting(0);
        $content = file_get_contents($file->getRealpath());
        error_reporting($level);
        if (false === $content) {
            $error = error_get_last();
            throw new RuntimeException($error['message']);
        }

        return $content;
    }
}
