<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DoctrineExtensionsBundle\Form;

use FSi\DoctrineExtensions\Uploadable\File;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationRequestHandler as BaseHttpFoundationRequestHandler;

class FSiUploadableRequestHandler extends BaseHttpFoundationRequestHandler
{
    public function isFileUpload($data)
    {
        return parent::isFileUpload($data) || ($data instanceof File);
    }
}
