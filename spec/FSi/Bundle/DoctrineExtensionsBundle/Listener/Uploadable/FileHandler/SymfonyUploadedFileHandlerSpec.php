<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable\FileHandler;

use FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable\FileHandler\SymfonyUploadedFileHandler;
use FSi\DoctrineExtensions\Uploadable\FileHandler\AbstractHandler;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

class SymfonyUploadedFileHandlerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(SymfonyUploadedFileHandler::class);
    }

    function it_should_be_file_handler()
    {
        $this->shouldBeAnInstanceOf(AbstractHandler::class);
    }

    function it_should_supports_symfony_uploaded_file(UploadedFile $file)
    {
        $this->supports($file)->shouldReturn(true);
    }

    function it_should_return_original_file_name(UploadedFile $file)
    {
        $file->getClientOriginalName()->shouldBeCalled()->willReturn('name.jpg');
        $this->getName($file)->shouldReturn('name.jpg');
    }
}

class UploadedFile extends SymfonyUploadedFile
{
    public function __construct()
    {
        /* There are some issues in prophecy and its not possible to create double that implements UploadedFile. */
    }
}
