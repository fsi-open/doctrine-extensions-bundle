<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\Resolver;

use FSi\DoctrineExtensions\Uploadable\File;
use Gaufrette\Adapter\Cache;
use Gaufrette\Adapter\Local;

class FSiFilePathResolver
{
    private $adapterPath;
    private $filePrefix;

    /**
     * @param string $adapterPath path for local adapter created when File adapter is different than local or cache.
     * @param string $filePrefix
     */
    public function __construct($adapterPath, $filePrefix)
    {
        $this->adapterPath = $adapterPath;
        $this->filePrefix = $filePrefix;
    }

    /**
     * @param \FSi\DoctrineExtensions\Uploadable\File $file
     * @param null|string $prefix
     * @throws \RuntimeException
     * @return string
     */
    public function filePath(File $file, $prefix = null)
    {
        if ($file->getFilesystem()->getAdapter() instanceof Local
            || $file->getFilesystem()->getAdapter() instanceof Cache) {
            return  '/' . $this->generatePath($file, $prefix);
        }

        $this->writeFileToLocalAdapter($file);

        return '/' . $this->generatePath($file, $prefix);
    }

    /**
     * @param \FSi\DoctrineExtensions\Uploadable\File $file
     * @return string
     */
    public function fileBasename(File $file)
    {
        return basename($file->getName());
    }

    /**
     * @param \FSi\DoctrineExtensions\Uploadable\File $file
     * @throws \RuntimeException
     * @throws \OutOfBoundsException
     * @throws \Gaufrette\Exception\FileNotFound
     */
    protected function writeFileToLocalAdapter(File $file)
    {
        if (!$this->getLocalAdapter()->exists($file->getKey())) {
            $this->getLocalAdapter()->write($file->getKey(), $file->getContent());
        }
    }

    /**
     * @return \Gaufrette\Adapter\Local
     */
    protected function getLocalAdapter()
    {
        if (!isset($this->adapter)) {
            $this->adapter = new Local($this->adapterPath, true);
        }

        return $this->adapter;
    }

    /**
     * @param \FSi\DoctrineExtensions\Uploadable\File $file
     * @param null $prefix
     * @return string
     */
    protected function generatePath(File $file, $prefix = null)
    {
        if (!isset($prefix) && isset($this->filePrefix)) {
            $prefix = $this->filePrefix;
        }

        if (isset($prefix)) {
            $prefix = trim($prefix, '/');

            return $prefix . '/' . ltrim($file->getKey(), '/');
        }

        return $file->getKey();
    }
} 
