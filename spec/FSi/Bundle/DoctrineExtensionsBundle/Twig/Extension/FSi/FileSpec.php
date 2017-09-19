<?php

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\Twig\Extension\FSi;

use FSi\DoctrineExtensions\Uploadable\File;
use PhpSpec\ObjectBehavior;

class FileSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('FSi\Bundle\DoctrineExtensionsBundle\Twig\Extension\FSi\File');
    }

    function it_is_twig_extension()
    {
        $this->shouldBeAnInstanceOf('Twig_Extension');
    }

    function it_have_fsi_url_name()
    {
        $this->getName()->shouldReturn('fsi_file');
    }

    function it_have_is_fsi_file_function()
    {
        $this->getFunctions()->shouldHaveFunction('is_fsi_file');
    }

    function it_recognize_fsi_file(File $file)
    {
        $this->isFSiFile('thisisnotfile')->shouldReturn(false);
        $this->isFSiFile(new \DateTime())->shouldReturn(false);
        $this->isFSiFile($file)->shouldReturn(true);
    }

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
        ];
    }
}
