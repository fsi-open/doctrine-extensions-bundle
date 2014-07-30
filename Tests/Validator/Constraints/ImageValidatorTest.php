<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\Tests\Validator\Constraints;

use FSi\Bundle\DoctrineExtensionsBundle\Validator\Constraints\ImageValidator;
use FSi\DoctrineExtensions\Uploadable\File;
use Gaufrette\Adapter\Local;
use Gaufrette\Filesystem;
use Symfony\Component\Validator\Tests\Constraints\ImageValidatorTest as BaseTest;

class ImageValidatorTest extends \PHPUnit_Framework_TestCase
{
    private $symfonyValidator;

    /** @var \Gaufrette\Filesystem */
    private $filesystem;

    protected function setUp()
    {
        $reflection = new \ReflectionClass('Symfony\Component\Validator\Tests\Constraints\ImageValidatorTest');
        $adapter = new Local(dirname($reflection->getFileName()) . '/Fixtures/');
        $this->filesystem = new Filesystem($adapter);

        $this->context = $this->getMock('Symfony\Component\Validator\ExecutionContext', array(), array(), '', false);
        $this->symfonyValidator = $this->getMock('Symfony\Component\Validator\Constraints\FileValidator');
        $this->validator = new ImageValidator($this->symfonyValidator);
        $this->validator->initialize($this->context);
        /** @var File image */
        $this->image = new File('test.gif', $this->filesystem);
        $this->imageLandscape = new File('test_landscape.gif', $this->filesystem);
        $this->imagePortrait = new File('test_portrait.gif', $this->filesystem);
    }

    public function testValidateFSiImageFile()
    {
        $that = $this;
        /** @var File $file */
        file_put_contents($fileName = '/tmp/'.uniqid(), $this->image->getContent());

        $this->symfonyValidator
            ->expects($this->any())
            ->method('validate')
            ->with($this->anything(), $constraintMock = $this->getMock('Symfony\Component\Validator\Constraint'))
            ->will($this->returnCallback(function($file, $constraint) use ($fileName, $that) {
                $that->assertFileEquals($file, $fileName);
            }))
        ;

        $this->validator->validate($fileName, $constraintMock);
    }

    public function testValidateNotFSiImageFile()
    {
        $that = $this;
        file_put_contents($fileName = '/tmp/'.uniqid(), $this->image->getContent());
        $this->symfonyValidator
            ->expects($this->any())
            ->method('validate')
            ->with($this->equalTo($fileName), $constraintMock = $this->getMock('Symfony\Component\Validator\Constraint'))
            ->will($this->returnCallback(function($path, $constraint) use ($fileName, $that) {
                $that->assertFileEquals($path, $fileName);
            }))
        ;

        $this->validator->validate($fileName, $constraintMock);
    }

    public function testValidateWronglyFSiFile()
    {
        $constraint = new \Symfony\Component\Validator\Constraints\Image(array(
            'maxHeight'           => '100',
        ));

        $this->symfonyValidator
            ->expects($this->any())
            ->method('validate')
            ->with($this->anything(), $constraint)
            ->will($this->throwException(new \Exception))
        ;

        $this->setExpectedException('\Exception');
        $this->validator->validate($this->image, $constraint);
    }
}
