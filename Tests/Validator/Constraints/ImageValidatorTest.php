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

class ImageValidatorTest extends BaseTest
{
    protected function setUp()
    {
        parent::setUp();

        $reflection = new \ReflectionClass('Symfony\Component\Validator\Tests\Constraints\ImageValidatorTest');
        $adapter = new Local(dirname($reflection->getFileName()) . '/Fixtures/');
        $filesystem = new Filesystem($adapter);

        $this->image = new File('test.gif', $filesystem);
        $this->imageLandscape = new File('test_landscape.gif', $filesystem);
        $this->imagePortrait = new File('test_portrait.gif', $filesystem);
    }

    protected function createValidator()
    {
        return new ImageValidator();
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
