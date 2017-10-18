<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\Twig;

use FSi\Bundle\DoctrineExtensionsBundle\Resolver\FSiFilePathResolver;
use FSi\DoctrineExtensions\Uploadable\File;
use PhpSpec\ObjectBehavior;

class FilesExtensionSpec extends ObjectBehavior
{
    function let(FSiFilePathResolver $filePathResolver)
    {
        $this->beConstructedWith($filePathResolver);
    }

    function it_is_twig_extension()
    {
        $this->shouldBeAnInstanceOf('Twig_Extension');
    }

    function it_has_extension_name()
    {
        $this->getName()->shouldReturn('fsi_files');
    }

    function it_has_fsi_functions()
    {
        $this->getFunctions()->shouldHaveFunction('is_fsi_file');
        $this->getFunctions()->shouldHaveFunction('fsi_file_url');
    }

    function it_has_fsi_file_basename_filter()
    {
        $this->getFilters()->shouldHaveFilter('fsi_file_basename');
    }

    function it_recognizes_fsi_file(File $file)
    {
        $this->isFSiFile('thisisnotfile')->shouldReturn(false);
        $this->isFSiFile(new \DateTime())->shouldReturn(false);
        $this->isFSiFile($file)->shouldReturn(true);
    }

    /**
     * @return array|bool
     */
    public function getMatchers()
    {
        return [
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
        ];
    }
}
