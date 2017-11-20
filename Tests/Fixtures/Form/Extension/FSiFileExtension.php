<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\Tests\Fixtures\Form\Extension;

use FSi\Bundle\DoctrineExtensionsBundle\Form\EventListener\RemovableFileSubscriber;
use FSi\Bundle\DoctrineExtensionsBundle\Form\FSiUploadableRequestHandler;
use FSi\Bundle\DoctrineExtensionsBundle\Form\Type\FSi\FileType;
use FSi\Bundle\DoctrineExtensionsBundle\Form\Type\FSi\RemovableFileType;
use FSi\Bundle\DoctrineExtensionsBundle\Form\TypeExtension\FileUploadFormExtension;
use Symfony\Component\Form\AbstractExtension;
use Symfony\Component\PropertyAccess\PropertyAccess;

class FSiFileExtension extends AbstractExtension
{
    protected function loadTypes()
    {
        return [
            new FileType(),
            new RemovableFileType(new RemovableFileSubscriber(PropertyAccess::createPropertyAccessor()))
        ];
    }

    protected function loadTypeExtensions()
    {
        return [
            new FileUploadFormExtension(new FSiUploadableRequestHandler()),
        ];
    }
}
