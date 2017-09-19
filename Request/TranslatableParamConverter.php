<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\Request;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\NoResultException;
use FSi\DoctrineExtensions\Translatable\Mapping\ClassMetadata as TranslatableClassMetadata;
use FSi\DoctrineExtensions\Translatable\Model\TranslatableRepositoryInterface;
use FSi\DoctrineExtensions\Translatable\TranslatableListener;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TranslatableParamConverter implements ParamConverterInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ManagerRegistry
     */
    protected $registry;

    /**
     * @var \FSi\DoctrineExtensions\Translatable\TranslatableListener
     */
    protected $translatableListener;

    /**
     * @param \Doctrine\Common\Persistence\ManagerRegistry $registry
     * @param \FSi\DoctrineExtensions\Translatable\TranslatableListener $translatableListener
     */
    public function __construct(ManagerRegistry $registry, TranslatableListener $translatableListener)
    {
        $this->registry = $registry;
        $this->translatableListener = $translatableListener;
    }

    /**
     * {@inheritdoc}
     *
     * @throws NotFoundHttpException When object not found
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $criteria = $this->buildSearchCriteria($configuration, $request);
        if (empty($criteria)) {
            return false;
        }

        try {
            $object = $this->getRepository($configuration)
                ->findTranslatableOneBy(
                    $criteria,
                    null,
                    $request->getLocale()
                );
        } catch (NoResultException $e) {
            throw new NotFoundHttpException(sprintf(
                'Object of class "%s" has not been found.',
                $configuration->getClass()
            ));
        }

        $request->attributes->set($configuration->getName(), $object);

        return true;
    }
    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        return $this->validateClass($configuration) &&
            $this->validateManager($configuration) &&
            $this->validateRepository($configuration) &&
            $this->hasTranslatableProperties($configuration);
    }

    /**
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     */
    private function getMapping(ParamConverter $configuration, Request $request)
    {
        $mapping = $this->getMappingOption($configuration);
        if (empty($mapping)) {
            $keys = $request->attributes->keys();
            $mapping = $keys ? array_combine($keys, $keys) : [];
        }

        $exclude = $this->getExcludeOption($configuration);
        foreach ($exclude as $excluded) {
            unset($mapping[$excluded]);
        }

        return $mapping;
    }

    /**
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration
     * @return array
     */
    private function getMappingOption(ParamConverter $configuration)
    {
        $options = $configuration->getOptions();

        if (isset($options['mapping'])) {
            return $options['mapping'];
        }

        return [];
    }

    /**
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration
     * @return array
     */
    private function getExcludeOption(ParamConverter $configuration)
    {
        $options = $configuration->getOptions();

        if (isset($options['exclude'])) {
            return $options['exclude'];
        }

        return [];
    }

    /**
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration
     * @return \Doctrine\Common\Persistence\ObjectManager|null
     */
    private function getManager(ParamConverter $configuration)
    {
        $options = $configuration->getOptions();

        if (isset($options['entity_manager'])) {
            return $this->registry->getManager($options['entity_manager']);
        }

        return $this->registry->getManagerForClass($configuration->getClass());
    }

    /**
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     */
    private function buildSearchCriteria(ParamConverter $configuration, Request $request)
    {
        $mapping = $this->getMapping($configuration, $request);
        $metadata = $this->getDoctrineMetadata($configuration);
        $translatableMetadata = $this->getTranslatableMetadata($configuration);

        $criteria = [];
        foreach ($mapping as $attribute => $field) {
            if ($this->isFieldSearchable($metadata, $field) || $this->isFieldTranslatable($translatableMetadata, $field)) {
                $criteria[$field] = $request->attributes->get($attribute);
            }
        }

        return $this->filterNullCriteria($criteria, $configuration);
    }

    /**
     * @param array $criteria
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration
     * @return array
     */
    private function filterNullCriteria(array $criteria, ParamConverter $configuration)
    {
        if ($this->getStripNullOption($configuration)) {
            return array_filter($criteria, function ($value) { return !is_null($value); });
        }

        return $criteria;
    }

    /**
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration
     * @return bool
     */
    private function getStripNullOption(ParamConverter $configuration)
    {
        $options = $configuration->getOptions();

        if (isset($options['strip_null'])) {
            return (bool) $options['strip_null'];
        }

        return false;
    }

    /**
     * @param \Doctrine\Common\Persistence\Mapping\ClassMetadata $classMetadata
     * @param string $field
     * @return bool
     */
    private function isFieldSearchable(ClassMetadata $classMetadata, $field)
    {
        return $classMetadata->hasField($field) ||
            ($classMetadata->hasAssociation($field) && $classMetadata->isSingleValuedAssociation($field));
    }

    /**
     * @param \FSi\DoctrineExtensions\Translatable\Mapping\ClassMetadata $translatableMetadata
     * @param string $field
     * @return bool
     */
    private function isFieldTranslatable(TranslatableClassMetadata $translatableMetadata, $field)
    {
        foreach ($translatableMetadata->getTranslatableProperties() as $translationAssociation => $properties) {
            if (isset($properties[$field])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration
     * @return \FSi\DoctrineExtensions\Translatable\Entity\Repository\TranslatableRepository
     */
    private function getRepository(ParamConverter $configuration)
    {
        return $this->getManager($configuration)
            ->getRepository($configuration->getClass());
    }

    /**
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration
     * @return bool
     */
    private function validateClass(ParamConverter $configuration)
    {
        return null !== $configuration->getClass();
    }

    /**
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration
     * @return bool
     */
    private function validateManager(ParamConverter $configuration)
    {
        $manager = $this->getManager($configuration);

        if (null === $manager) {
            return false;
        }

        return !$manager->getMetadataFactory()->isTransient($configuration->getClass());
    }

    /**
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration
     * @return bool
     */
    private function validateRepository(ParamConverter $configuration)
    {
        return $this->getRepository($configuration) instanceof TranslatableRepositoryInterface;
    }

    /**
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration
     * @return \Doctrine\Common\Persistence\Mapping\ClassMetadata
     */
    private function getDoctrineMetadata(ParamConverter $configuration)
    {
        return $this->getManager($configuration)->getClassMetadata($configuration->getClass());
    }

    /**
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration
     * @return \FSi\DoctrineExtensions\Translatable\Mapping\ClassMetadata $translatableMetadata
     */
    private function getTranslatableMetadata(ParamConverter $configuration)
    {
        return $this->translatableListener->getExtendedMetadata(
            $this->getManager($configuration),
            $configuration->getClass()
        );
    }

    /**
     * @param \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration
     * @return bool
     */
    private function hasTranslatableProperties(ParamConverter $configuration)
    {
        return $this->getTranslatableMetadata($configuration)->hasTranslatableProperties();
    }
}
