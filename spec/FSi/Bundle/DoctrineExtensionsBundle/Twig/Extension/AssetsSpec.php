<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\Twig\Extension;

use FSi\Bundle\DoctrineExtensionsBundle\Resolver\FSiFilePathResolver;
use FSi\DoctrineExtensions\Uploadable\File;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Bundle\TwigBundle\Extension\AssetsExtension;

class AssetsSpec extends ObjectBehavior
{
    function let(\Twig_Environment $environment, AssetsExtension $assets, FSiFilePathResolver $filePathResolver)
    {
        $this->beConstructedWith($filePathResolver);
        $environment->hasExtension('assets')->shouldBeCalled()->willReturn(true);
        $environment->getExtension('assets')->shouldBeCalled()->willReturn($assets);
        $environment->getGlobals()->shouldBeCalled()->willReturn(array());
        $this->initRuntime($environment);
    }

    function it_is_twig_extension()
    {
        $this->shouldBeAnInstanceOf('Twig_Extension');
    }

    function it_have_extension_name()
    {
        $this->getName()->shouldReturn('fsi_assets');
    }

    function it_have_fsi_file_asset_function()
    {
        $this->getFunctions()->shouldHaveFunction('fsi_file_asset');
    }

    function it_compute_file_asset_path(File $file, AssetsExtension $assets, FSiFilePathResolver $filePathResolver)
    {
        $file->getKey()->willReturn('file-name.txt');
        $filePathResolver->fileAsset($file, 'uploaded')->willReturn('uploaded/file-name.txt');
        $assets->getAssetUrl('uploaded/file-name.txt')->willReturn('/uploaded/file-name.txt');

        $this->fileAsset($file, 'uploaded')->shouldReturn('/uploaded/file-name.txt');
    }

    function it_have_fsi_file_path_function()
    {
        $this->getFunctions()->shouldHaveFunction('fsi_file_path');
    }

    function it_have_fsi_file_basename_filter()
    {
        $this->getFilters()->shouldHaveFilter('fsi_file_basename');
    }

    /**
     * @return array|bool
     */
    public function getMatchers()
    {
        return array(
            'haveFunction' => function($subject, $key) {
                foreach ($subject as $function) {
                    if ($function instanceof \Twig_SimpleFunction) {
                        if ($function->getName() == $key) {
                            return true;
                        }
                    }
                }

                return false;
            },
            'haveFilter' => function($subject, $key) {
                foreach ($subject as $filter) {
                    if ($filter instanceof \Twig_SimpleFilter) {
                        if ($filter->getName() == $key) {
                            return true;
                        }
                    }
                }

                return false;
            }
        );
    }
}
