<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DoctrineExtensionsBundle\Validator\Constraints;

use FSi\DoctrineExtensions\Uploadable\File as FSiFile;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\FileValidator as BaseValidator;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Throwable;

class FileValidator extends ConstraintValidator
{
    /**
     * @var BaseValidator
     */
    private $symfonyValidator;

    public function __construct(BaseValidator $symfonyValidator)
    {
        $this->symfonyValidator = $symfonyValidator;
    }

    public function initialize(ExecutionContextInterface $context)
    {
        parent::initialize($context);

        $this->symfonyValidator->initialize($context);
    }

    public function validate($value, Constraint $constraint)
    {
        if ($value instanceof FSiFile) {
            $tmpFile = sys_get_temp_dir() . '/' . uniqid();
            file_put_contents($tmpFile, $value->getContent());

            try {
                $this->symfonyValidator->validate($tmpFile, $constraint);
            } catch (Throwable $e) {
                throw $e;
            } finally {
                unlink($tmpFile);
            }

            return;
        }

        $this->symfonyValidator->validate($value, $constraint);
    }
}
