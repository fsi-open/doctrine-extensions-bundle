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
use Symfony\Component\Validator\Constraints\File as FileConstraint;
use Symfony\Component\Validator\Tests\Constraints\FileValidatorObjectTest as BaseTest;

class FileValidatorObjectTest extends BaseTest
{
    protected function setUp()
    {
        parent::setUp();

        if (!class_exists('Symfony\Component\HttpFoundation\File\UploadedFile')) {
            $this->markTestSkipped('The "HttpFoundation" component is not available');
        }

        $this->path = sys_get_temp_dir().DIRECTORY_SEPARATOR.'FileValidatorTest';
        $this->file = fopen($this->path, 'w');
    }

    protected function createValidator()
    {
        return new FileValidator();
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

        $this->validator->validate($this->getFile($this->path), $constraint);

        $this->assertViolation('myMessage', array(
           '{{ limit }}' => '10',
           '{{ size }}' => '11',
           '{{ suffix }}' => 'bytes',
        ));
    }

    public function testTooLargeKiloBytes()
    {
        fwrite($this->file, str_repeat('0', 1400));

        $constraint = new \Symfony\Component\Validator\Constraints\File(array(
            'maxSize'           => '1k',
            'maxSizeMessage'    => 'myMessage',
        ));

        $this->validator->validate($this->getFile($this->path), $constraint);

        $this->assertViolation('myMessage', array(
            '{{ limit }}' => '1',
            '{{ size }}' => '1.4',
            '{{ suffix }}' => 'kB',
        ));
    }

    public function testTooLargeMegaBytes()
    {
        fwrite($this->file, str_repeat('0', 1400000));

        $constraint = new \Symfony\Component\Validator\Constraints\File(array(
            'maxSize'           => '1M',
            'maxSizeMessage'    => 'myMessage',
        ));

        $this->validator->validate($this->getFile($this->path), $constraint);

        $this->assertViolation('myMessage', array(
            '{{ limit }}' => '1',
            '{{ size }}' => '1.4',
            '{{ suffix }}' => 'MB',
        ));
    }

    protected function assertViolation($message, array $parameters = array(), $propertyPath = 'property.path', $invalidValue = 'InvalidValue', $plural = null, $code = null)
    {
        $violationParameters = $this->context->getViolations()->get(0)->getMessageParameters();
        if (isset($violationParameters['{{ file }}'])) {
            $parameters['{{ file }}'] = $violationParameters['{{ file }}'];
        }

        parent::assertViolation($message, $parameters, $propertyPath, $invalidValue, $plural);
    }
}
