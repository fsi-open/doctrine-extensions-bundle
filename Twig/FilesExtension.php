<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DoctrineExtensionsBundle\Twig;

use FSi\Bundle\DoctrineExtensionsBundle\Resolver\FSiFilePathResolver;
use FSi\DoctrineExtensions\Uploadable\File as FSiFile;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class FilesExtension extends AbstractExtension
{
    /**
     * @var FSiFilePathResolver
     */
    protected $filePathResolver;

    public function __construct(FSiFilePathResolver $filePathResolver)
    {
        $this->filePathResolver = $filePathResolver;
    }

    public function getName()
    {
        return 'fsi_files';
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('is_fsi_file', [$this, 'isFSiFile']),
            new TwigFunction('fsi_file_url', [$this->filePathResolver, 'fileUrl'])
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('fsi_file_basename', [$this->filePathResolver, 'fileBasename'])
        ];
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function isFSiFile($value): bool
    {
        if (!is_object($value)) {
            return false;
        }

        return $value instanceof FSiFile;
    }
}
