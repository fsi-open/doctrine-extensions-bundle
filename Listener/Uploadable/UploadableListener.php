<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable;

use Doctrine\ORM\EntityManagerInterface;
use FSi\DoctrineExtensions\Metadata\ClassMetadataInterface;
use FSi\DoctrineExtensions\Uploadable\FileHandler\FileHandlerInterface;
use FSi\DoctrineExtensions\Uploadable\Mapping\ClassMetadata;
use FSi\DoctrineExtensions\Uploadable\UploadableListener as BaseListener;

class UploadableListener extends BaseListener
{
    /**
     * [
     *    'FSi\Bundle\Demo\Bundle\Entity\Article' => [
     *        'property' => [
     *            'filesystem' => 'filesystem_name',
     *            'keymaker' => 'keymaker_name'
     *        ]
     *    ]
     * ]
     *
     * @var array
     */
    protected $configuration;

    public function __construct($filesystems, FileHandlerInterface $fileHandler, array $configuration = [])
    {
        parent::__construct($filesystems, $fileHandler);
        $this->setConfiguration($configuration);
    }

    public function getSubscribedEvents()
    {
        return [
            'preFlush',
            'postPersist',
            'postFlush',
            'postRemove',
        ];
    }

    public function getExtendedMetadata(EntityManagerInterface $objectManager, string $class): ClassMetadataInterface
    {
        $class = $this->normalizeClass($class);

        $factory = $this->getExtendedMetadataFactory($objectManager);
        /** @var ClassMetadata $extendedMetadata */
        $extendedMetadata = $factory->getClassMetadata($class);

        if (array_key_exists($class, $this->configuration)) {
            $properties = $extendedMetadata->getUploadableProperties();

            foreach ($this->configuration[$class] as $property => $configuration) {
                if (!array_key_exists($property, $properties)) {
                    $properties[$property] = [
                        'targetField' => '',
                        'filesystem' => '',
                        'keymaker' => null,
                        'keyLength' => null,
                        'keyPattern' => '',
                    ];
                }

                if (array_key_exists('filesystem', $configuration)) {
                    $properties[$property]['filesystem'] = $configuration['filesystem'];
                }

                if (array_key_exists('keymaker', $configuration)) {
                    $properties[$property]['keymaker'] = $configuration['keymaker'];
                }

                if (array_key_exists('keyPattern', $configuration)) {
                    $properties[$property]['keyPattern'] = $configuration['keyPattern'];
                }
            }

            foreach ($properties as $property => $configuration) {
                $extendedMetadata->addUploadableProperty(
                    $property,
                    $properties[$property]['targetField'],
                    $properties[$property]['filesystem'],
                    $properties[$property]['keymaker'],
                    $properties[$property]['keyLength'],
                    $properties[$property]['keyPattern']
                );
            }
        }

        $this->validateExtendedMetadata($objectManager->getClassMetadata($class), $extendedMetadata);

        return $extendedMetadata;
    }

    protected function setConfiguration(array $configuration): void
    {
        $this->configuration = [];

        foreach ($configuration as $class => $config) {
            $className = $this->normalizeClass($class);
            $this->configuration[$className] = $config;
        }
    }

    private function normalizeClass($class): string
    {
        return ltrim($class, '\\');
    }
}
