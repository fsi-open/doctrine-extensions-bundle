<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\Tests\Listener\Uploadable\FileHandler;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable\FileHandler\SymfonyUploadedFileHandler;

class SymfonyUploadedFileHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testWronglyUploadedFile()
    {
        $file = new UploadedFile('/foo/bar', 'baz.jpg', null, null, 1, true);
        $handler = new SymfonyUploadedFileHandler();

        $this->assertTrue($handler->supports($file));

        $this->setExpectedException('FSi\Bundle\DoctrineExtensionsBundle\Exception\Uploadable\InvalidFile');
        $handler->getContent($file);
    }
}
