<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DoctrineExtensionsBundle\Tests\Listener\Uploadable\FileHandler;

use FSi\Bundle\DoctrineExtensionsBundle\Exception\Uploadable\InvalidFileException;
use FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable\FileHandler\SymfonyUploadedFileHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SymfonyUploadedFileHandlerTest extends TestCase
{
    public function testWronglyUploadedFile()
    {
        $file = new UploadedFile('/foo/bar', 'baz.jpg', null, null, 1, true);
        $handler = new SymfonyUploadedFileHandler();

        $this->assertTrue($handler->supports($file));

        $this->expectException(InvalidFileException::class);
        $handler->getContent($file);
    }
}
