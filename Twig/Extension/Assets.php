<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\Twig\Extension;

use FSi\Bundle\DoctrineExtensionsBundle\Resolver\FilePathResolver;
use FSi\DoctrineExtensions\Uploadable\File;
use Twig_Environment;

class Assets extends \Twig_Extension
{
    /**
     * @var \Symfony\Bundle\TwigBundle\Extension\AssetsExtension
     */
    protected $assets;

    /**
     * @var null|string
     */
    protected $filePrefix;

    /**
     * @var \FSi\Bundle\DoctrineExtensionsBundle\Resolver\FilePathResolver
     */
    protected $filePathResolver;

    /**
     * @param FilePathResolver $filePathResolver
     */
    public function __construct(FilePathResolver $filePathResolver)
    {
        $this->filePathResolver = $filePathResolver;
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
           new \Twig_SimpleFunction('fsi_file_path', array($this->filePathResolver, 'filePath'))
        );
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('fsi_file_basename', array($this->filePathResolver, 'fileBasename'))
        );
    }

    public function fileAsset(File $file, $prefix = null)
    {
        return $this->assets->getAssetUrl(
            $this->filePathResolver->fileAsset($file, $prefix)
        );
    }
}
