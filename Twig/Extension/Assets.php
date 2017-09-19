<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\Twig\Extension;

use FSi\Bundle\DoctrineExtensionsBundle\Resolver\FSiFilePathResolver;
use FSi\DoctrineExtensions\Uploadable\File;
use Twig_Environment;

class Assets extends \Twig_Extension implements \Twig_Extension_InitRuntimeInterface
{
    /**
     * @var \Symfony\Bundle\TwigBundle\Extension\AssetsExtension|\Symfony\Bridge\Twig\Extension\AssetExtension
     */
    protected $assets;

    /**
     * @var null|string
     */
    protected $filePrefix;

    /**
     * @var \FSi\Bundle\DoctrineExtensionsBundle\Resolver\FSiFilePathResolver
     */
    protected $filePathResolver;

    /**
     * @param FSiFilePathResolver $filePathResolver
     */
    public function __construct(FSiFilePathResolver $filePathResolver)
    {
        $this->filePathResolver = $filePathResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function initRuntime(Twig_Environment $environment)
    {
        if ($environment->hasExtension('\Symfony\Bridge\Twig\Extension\AssetExtension')) {
            $this->assets = $environment->getExtension('\Symfony\Bridge\Twig\Extension\AssetExtension');
        } elseif ($environment->hasExtension('assets')) {
            $this->assets = $environment->getExtension('assets');
        } else {
            throw new \Twig_Error("Assets extension must be loaded.");
        }

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
        return [
           new \Twig_SimpleFunction('fsi_file_asset', [$this, 'fileAsset']),
           new \Twig_SimpleFunction('fsi_file_path', [$this->filePathResolver, 'filePath']),
           new \Twig_SimpleFunction('fsi_file_url', [$this->filePathResolver, 'fileUrl'])
        ];
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('fsi_file_basename', [$this->filePathResolver, 'fileBasename'])
        ];
    }

    public function fileAsset(File $file, $prefix = null)
    {
        return $this->assets->getAssetUrl(
            $this->filePathResolver->filePath($file, $prefix)
        );
    }
}
