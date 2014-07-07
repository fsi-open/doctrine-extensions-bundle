<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\FileValidator as BaseValidator;
use FSi\DoctrineExtensions\Uploadable\File as FSiFile;

class FileValidator extends BaseValidator
{
    protected static $suffices = array(
        1 => 'bytes',
        self::KB_BYTES => 'kB',
        self::MB_BYTES => 'MB',
    );

    /**
     * {@inheritdoc}
     */
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
