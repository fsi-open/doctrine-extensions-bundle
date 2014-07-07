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

class FileValidatorObjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Validator\ExecutionContext
     */
    protected $context;

    /**
     * @var \Symfony\Component\Validator\Constraints\FileValidator
     */
    protected $symfonyValidator;

    protected function setUp()
    {
        if (!class_exists('Symfony\Component\HttpFoundation\File\UploadedFile')) {
            $this->markTestSkipped('The "HttpFoundation" component is not available');
        }

        $this->context = $this->getMock('Symfony\Component\Validator\ExecutionContext', array(), array(), '', false);
        $this->symfonyValidator = $this->getMock('Symfony\Component\Validator\Constraints\FileValidator');

        $this->validator = new FileValidator($this->symfonyValidator);
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

    public function testValidateFSiFile()
    {
        $file = $this->path;
        $that = $this;

        $this->symfonyValidator
            ->expects($this->any())
            ->method('validate')
            ->with($this->anything(), $constraintMock = $this->getMock('Symfony\Component\Validator\Constraint'))
            ->will($this->returnCallback(function($path, $constraint) use ($file, $that) {
                $that->assertFileEquals($path, $file);
            }))
        ;

        $this->validator->validate($this->getFile($this->path), $constraintMock);
    }

    public function testValidateNotFSiFile()
    {
        $file = $this->path;
        $that = $this;
        $this->symfonyValidator
            ->expects($this->any())
            ->method('validate')
            ->with($this->equalTo($this->path), $constraintMock = $this->getMock('Symfony\Component\Validator\Constraint'))
            ->will($this->returnCallback(function($path, $constraint) use ($file, $that) {
                $that->assertFileEquals($path, $file);
            }))
        ;

        $this->validator->validate($this->path, $constraintMock);

    }

    public function testValidateWronglyFSiFile()
    {
        $constraint = new \Symfony\Component\Validator\Constraints\File(array(
            'maxSize'           => '1',
            'maxSizeMessage'    => 'Too much input data',
        ));

        fwrite($this->file, bin2hex(openssl_random_pseudo_bytes(10000)));
        fclose($this->file);

        $this->symfonyValidator
            ->expects($this->any())
            ->method('validate')
            ->with($this->anything(), $constraint)
            ->will($this->throwException(new \Exception))
        ;

        $this->setExpectedException('\Exception');
        $this->validator->validate($this->getFile($this->path), $constraint);
    }
}
