<?php

/**
 * (c) Fabryka Stron Internetowych sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\Listener\Uploadable;

use Doctrine\Common\Persistence\ObjectManager;
use FSi\DoctrineExtensions\Uploadable\FileHandler\FileHandlerInterface;
use FSi\DoctrineExtensions\Uploadable\UploadableListener as BaseListener;

/**
 * @author Norbert Orzechowicz <norbert@fsi.pl>
 */
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

    /**
     * @param array|\Gaufrette\FilesystemMap $filesystems
     * @param FileHandlerInterface $fileHandler
     * @param array $configuration
     */
    public function __construct($filesystems, FileHandlerInterface $fileHandler, $configuration = array())
    {
        parent::__construct($filesystems, $fileHandler);
        $this->setConfiguration($configuration);
    }

    /**
     * @param ObjectManager $objectManager
     * @param string $class
     * @return \FSi\DoctrineExtensions\Uploadable\Mapping\ClassMetadata
     */
    public function getExtendedMetadata(ObjectManager $objectManager, $class)
    {
        /* @var $metadata \FSi\DoctrineExtensions\Uploadable\Mapping\ClassMetadata */
        $metadata = parent::getExtendedMetadata($objectManager, $class);

        $class = ltrim($class, '\\');

        if (array_key_exists($class, $this->configuration)) {
            $properties = $metadata->getUploadableProperties();

            foreach ($this->configuration as $property => $configuration) {
                if (array_key_exists($property, $properties)) {
                    if (isset($configuration['filesystem'])) {
                        $properties[$property]['filesystem'] = $configuration['filesystem'];
                    }

                    if (isset($configuration['keymaker'])) {
                        $properties[$property]['keymaker'] = $configuration['keymaker'];
                    }

                    if (isset($configuration['keyPattern'])) {
                        $properties[$property]['keyPattern'] = $configuration['keyPattern'];
                    }

                }
            }

            foreach ($properties as $property => $configuration) {
                $metadata->addUploadableProperty(
                    $property,
                    $properties[$property]['targetField'],
                    $properties[$property]['filesystem'],
                    $properties[$property]['keymaker'],
                    $properties[$property]['keyLength'],
                    $properties[$property]['keyPattern']
                );
            }
        }

        return $metadata;
    }

    /**
     * @param $configuration
     */
    protected function setConfiguration($configuration)
    {
        $this->configuration = array();

        foreach ($configuration as $class => $config) {
            $className = ltrim($class, '\\');
            $this->configuration[$className] = $config;
        }
    }
}