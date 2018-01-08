<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\Twig;

use DateTime;
use FSi\Bundle\DoctrineExtensionsBundle\Resolver\FSiFilePathResolver;
use FSi\DoctrineExtensions\Uploadable\File;
use PhpSpec\ObjectBehavior;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class FilesExtensionSpec extends ObjectBehavior
{
    function let(FSiFilePathResolver $filePathResolver)
    {
        $this->beConstructedWith($filePathResolver);
    }

    function it_is_twig_extension()
    {
        $this->shouldBeAnInstanceOf(AbstractExtension::class);
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
        $this->isFSiFile(new DateTime())->shouldReturn(false);
        $this->isFSiFile($file)->shouldReturn(true);
    }

    public function getMatchers(): array
    {
        return [
            'haveFunction' => function($subject, $key) {
                foreach ($subject as $function) {
                    if ($function instanceof TwigFunction && $function->getName() === $key) {
                        return true;
                    }
                }

                return false;
            },
            'haveFilter' => function($subject, $key) {
                foreach ($subject as $filter) {
                    if ($filter instanceof TwigFilter && $filter->getName() === $key) {
                        return true;
                    }
                }

                return false;
            }
        ];
    }
}
