<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\Tests\Validator\Constraints;

use FSi\Bundle\DoctrineExtensionsBundle\Validator\Constraints\FileValidator;
use FSi\DoctrineExtensions\Uploadable\File;
use Gaufrette\Adapter\Local;
use Gaufrette\Filesystem;
use Symfony\Component\Validator\Tests\Constraints\FileValidatorObjectTest as BaseTest;

class FileValidatorObjectTest extends BaseTest
{
    protected function setUp()
    {
        if (!class_exists('Symfony\Component\HttpFoundation\File\UploadedFile')) {
            $this->markTestSkipped('The "HttpFoundation" component is not available');
        }

        $this->context = $this->getMock('Symfony\Component\Validator\ExecutionContext', array(), array(), '', false);
        $this->validator = new FileValidator();
        $this->validator->initialize($this->context);
        $this->path = sys_get_temp_dir().DIRECTORY_SEPARATOR.'FileValidatorTest';
        $this->file = fopen($this->path, 'w');
    }

    /**
     * @param string $filename
     * @return \FSi\DoctrineExtensions\Uploadable\File
     */
    protected function getFile($filename)
    {
        $adapter = new Local(dirname($filename));
        $filesystem = new Filesystem($adapter);

        return new File(basename($filename), $filesystem);
    }

    public function testTooLargeBytes()
    {
        fwrite($this->file, str_repeat('0', 11));

        $constraint = new \Symfony\Component\Validator\Constraints\File(array(
            'maxSize'           => 10,
            'maxSizeMessage'    => 'myMessage',
        ));

        $this->context->expects($this->once())
            ->method('addViolation');

        $this->validator->validate($this->getFile($this->path), $constraint);
    }

    public function testTooLargeKiloBytes()
    {
        fwrite($this->file, str_repeat('0', 1400));

        $constraint = new \Symfony\Component\Validator\Constraints\File(array(
            'maxSize'           => '1k',
            'maxSizeMessage'    => 'myMessage',
        ));

        $this->context->expects($this->once())
            ->method('addViolation');

        $this->validator->validate($this->getFile($this->path), $constraint);
    }

    public function testTooLargeMegaBytes()
    {
        fwrite($this->file, str_repeat('0', 1400000));

        $constraint = new \Symfony\Component\Validator\Constraints\File(array(
            'maxSize'           => '1M',
            'maxSizeMessage'    => 'myMessage',
        ));

        $this->context->expects($this->once())
            ->method('addViolation');

        $this->validator->validate($this->getFile($this->path), $constraint);
    }

    /**
     * @dataProvider provideMaxSizeExceededTests
     */
    public function testMaxSizeExceeded($bytesWritten, $limit, $sizeAsString, $limitAsString, $suffix)
    {
        fseek($this->file, $bytesWritten-1, SEEK_SET);
        fwrite($this->file, '0');
        fclose($this->file);

        $constraint = new \Symfony\Component\Validator\Constraints\File(array(
            'maxSize'           => $limit,
            'maxSizeMessage'    => 'myMessage',
        ));

        $this->context->expects($this->once())
            ->method('addViolation')
           ;

        $this->validator->validate($this->getFile($this->path), $constraint);
    }
}
