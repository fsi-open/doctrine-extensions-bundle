<?php

namespace FSi\Bundle\DoctrineExtensionsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints\FileValidator as BaseValidator;
use FSi\DoctrineExtensions\Uploadable\File as FSiFile;

class FileValidator extends BaseValidator
{
    public function validate($value, Constraint $constraint)
    {
        if ($value instanceof FSiFile) {
            $tmpFile = sys_get_temp_dir() . '/' . uniqid();
            file_put_contents($tmpFile, $value->getContent());

            try {
                parent::validate($tmpFile, $constraint);
            } catch (\Exception $e) {
                unlink($tmpFile);
                throw $e;
            }

            return;
        }

        parent::validate($value, $constraint);
    }
}