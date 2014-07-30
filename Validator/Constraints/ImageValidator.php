<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\ImageValidator as BaseValidator;
use FSi\DoctrineExtensions\Uploadable\File as FSiFile;
use Symfony\Component\Validator\Constraints\FileValidator as SymfonyFileValidator;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ExecutionContextInterface;

class ImageValidator extends ConstraintValidator
{
    private $validator;

    public function __construct(
        SymfonyFileValidator $validator = null
    ) {
        if (!$validator) {
            $validator = new SymfonyFileValidator();
        }
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value instanceof FSiFile) {
            $tmpFile = sys_get_temp_dir() . '/' . uniqid();
            file_put_contents($tmpFile, $value->getContent());

            try {
                $this->validator->validate($tmpFile, $constraint);
            } catch (\Exception $e) {
                unlink($tmpFile);
                throw $e;
            }

            return;
        }

        $this->validator->validate($value, $constraint);
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ExecutionContextInterface $context)
    {
        $this->validator->initialize($context);
    }
}
