<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\Twig\Extension;

use FSi\DoctrineExtensions\Uploadable\File;
use Gaufrette\Adapter\Cache;
use Gaufrette\Adapter\Local;
use Twig_Environment;

class Assets extends \Twig_Extension
{
    /**
     * @var \Symfony\Bundle\TwigBundle\Extension\AssetsExtension
     */
    protected $assets;

    /**
     * @var string
     */
    protected $adapterPath;

    /**
     * @var null|string
     */
    protected $filePrefix;

    /**
     * @param string $adapterPath path for local adapter created when File adapter is different than local or cache.
     */
    public function __construct($adapterPath)
    {
        $this->adapterPath = $adapterPath;
    }

    /**
     * {@inheritdoc}
     */
    public function initRuntime(Twig_Environment $environment)
    {
        if (!$environment->hasExtension('assets')) {
            throw new \Twig_Error("assets extension must be loaded.");
        }

        $this->assets = $environment->getExtension('assets');
        $globals = $environment->getGlobals();

        if (array_key_exists('fsi_file_prefix', $globals)) {
            $this->filePrefix = $globals['fsi_file_prefix'];
        }
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
        return array(
           new \Twig_SimpleFunction('fsi_file_asset', array($this, 'fileAsset')),
           new \Twig_SimpleFunction('fsi_file_path', array($this, 'filePath'))
        );
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('fsi_file_basename', array($this, 'fileBasename'))
        );
    }

    /**
     * @param \FSi\DoctrineExtensions\Uploadable\File $file
     * @param null|string $prefix
     * @return string
     */
    public function fileAsset(File $file, $prefix = null)
    {
        if ($file->getFilesystem()->getAdapter() instanceof Local
            || $file->getFilesystem()->getAdapter() instanceof Cache) {
            return $this->assets->getAssetUrl($this->generatePath($file, $prefix));
        }

        $this->writeFileToLocalAdapter($file);

        return $this->assets->getAssetUrl($this->generatePath($file, $prefix));
    }

    /**
     * @param \FSi\DoctrineExtensions\Uploadable\File $file
     * @param null|string $prefix
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
