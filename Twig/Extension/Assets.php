<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\Twig\Extension;

use FSi\Bundle\DoctrineExtensionsBundle\Resolver\FSiFilePathResolver;
use FSi\DoctrineExtensions\Uploadable\File as FSiFile;
use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

class Assets extends Twig_Extension
{
    /**
     * @var FSiFilePathResolver
     */
    protected $filePathResolver;

    public function __construct(FSiFilePathResolver $filePathResolver)
    {
        $this->filePathResolver = $filePathResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'fsi_assets';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('is_fsi_file', [$this, 'isFSiFile']),
            new Twig_SimpleFunction('fsi_file_url', [$this->filePathResolver, 'fileUrl'])
        ];
    }

    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('fsi_file_basename', [$this->filePathResolver, 'fileBasename'])
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
