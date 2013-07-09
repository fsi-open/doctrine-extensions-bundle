<?php

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable\FileHandler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

class SymfonyUploadedFileHandlerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable\FileHandler\SymfonyUploadedFileHandler');
    }

    function it_should_be_file_handler()
    {
        $this->shouldBeAnInstanceOf('FSi\DoctrineExtensions\Uploadable\FileHandler\AbstractHandler');
    }

    /**
     * @param \spec\FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable\FileHandler\UploadedFile $file
     */
    function it_should_supports_symfony_uploaded_file($file)
    {
        $this->supports($file)->shouldReturn(true);
    }

    /**
     * @param \spec\FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable\FileHandler\UploadedFile $file
     */
    function it_should_return_original_file_name($file)
    {
        $file->getClientOriginalName()->shouldBeCalled()->willReturn('name.jpg');
        $this->getName($file)->shouldReturn('name.jpg');
    }
}

class UploadedFile extends SymfonyUploadedFile
{
    public function __construct()
    {
        /* There are some issues in prophecy and its not possible to create double that implements UploadedFile */
    }
}
