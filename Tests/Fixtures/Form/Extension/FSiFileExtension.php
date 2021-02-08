<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DoctrineExtensionsBundle\Tests\Fixtures\Form\Extension;

use FSi\Bundle\DoctrineExtensionsBundle\Form\EventListener\RemovableFileSubscriber;
use FSi\Bundle\DoctrineExtensionsBundle\Form\FSiUploadableRequestHandler;
use FSi\Bundle\DoctrineExtensionsBundle\Form\Type\FSi\FileType;
use FSi\Bundle\DoctrineExtensionsBundle\Form\Type\FSi\RemovableFileType;
use FSi\Bundle\DoctrineExtensionsBundle\Form\TypeExtension\FileUploadFormExtension;
use FSi\Bundle\DoctrineExtensionsBundle\Resolver\FSiFilePathResolver;
use Symfony\Component\Form\AbstractExtension;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FSiFileExtension extends AbstractExtension
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var FSiFilePathResolver
     */
    private $filePathResolver;

    public function __construct(UrlGeneratorInterface $urlGenerator, FSiFilePathResolver $filePathResolver)
    {
        $this->urlGenerator = $urlGenerator;
        $this->filePathResolver = $filePathResolver;
    }

    protected function loadTypes(): array
    {
        return [
            new FileType($this->urlGenerator, $this->filePathResolver),
            new RemovableFileType(new RemovableFileSubscriber(PropertyAccess::createPropertyAccessor()))
        ];
    }

    protected function loadTypeExtensions(): array
    {
        return [
            new FileUploadFormExtension(new FSiUploadableRequestHandler()),
        ];
    }
}
