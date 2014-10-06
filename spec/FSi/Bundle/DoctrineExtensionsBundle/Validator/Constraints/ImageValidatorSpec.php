<?php

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\Validator\Constraints;

use FSi\DoctrineExtensions\Uploadable\File as FSiFile;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\ImageValidator;

class ImageValidatorSpec extends ObjectBehavior
{
    function let(ImageValidator $imageValidator)
    {
        $this->beConstructedWith($imageValidator);
    }

    function it_is_constraint_validator()
    {
        $this->shouldBeAnInstanceOf('Symfony\Component\Validator\ConstraintValidator');
    }

    function it_calls_symfony_validator(ImageValidator $imageValidator, File $constraint)
    {
        $imageValidator->validate('path_to_a_file', $constraint)->shouldBeCalled();

        $this->validate('path_to_a_file', $constraint);
    }

    function it_saves_file_and_calls_symfony_validator(ImageValidator $imageValidator, File $constraint, FSiFile $file)
    {
        $file->getContent()->willReturn('file content');
        $imageValidator->validate(Argument::containingString('/tmp/'), $constraint)->shouldBeCalled();

        $this->validate($file, $constraint);
    }
}
