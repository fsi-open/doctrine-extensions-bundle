<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DoctrineExtensionsBundle\Request;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
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
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * @var TranslatableListener
     */
    private $translatableListener;

    public function __construct(ManagerRegistry $registry, TranslatableListener $translatableListener)
    {
        $this->registry = $registry;
        $this->translatableListener = $translatableListener;
    }

    /**
     * @param Request $request
     * @param ParamConverter $configuration
     * @return boolean
     * @throws NotFoundHttpException
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $criteria = $this->buildSearchCriteria($configuration, $request);
        if (empty($criteria)) {
            return false;
        }

        try {
            $object = $this->getRepository($configuration)->findTranslatableOneBy(
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

    public function supports(ParamConverter $configuration): bool
    {
        return $this->validateClass($configuration)
            && $this->validateManager($configuration)
            && $this->validateRepository($configuration)
            && $this->hasTranslatableProperties($configuration)
        ;
    }

    private function getMapping(ParamConverter $configuration, Request $request): array
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

    private function getMappingOption(ParamConverter $configuration): array
    {
        $options = $configuration->getOptions();

        if (isset($options['mapping'])) {
            return $options['mapping'];
        }

        return [];
    }

    private function getExcludeOption(ParamConverter $configuration): array
    {
        $options = $configuration->getOptions();

        if (isset($options['exclude'])) {
            return $options['exclude'];
        }

        return [];
    }

    private function getManager(ParamConverter $configuration): ?ObjectManager
    {
        $options = $configuration->getOptions();

        if (isset($options['entity_manager'])) {
            return $this->registry->getManager($options['entity_manager']);
        }

        return $this->registry->getManagerForClass($configuration->getClass());
    }

    private function buildSearchCriteria(ParamConverter $configuration, Request $request): array
    {
        $mapping = $this->getMapping($configuration, $request);
        $metadata = $this->getDoctrineMetadata($configuration);
        $translatableMetadata = $this->getTranslatableMetadata($configuration);

        $criteria = [];
        foreach ($mapping as $attribute => $field) {
            if ($this->isFieldSearchable($metadata, $field)
                || $this->isFieldTranslatable($translatableMetadata, $field)
            ) {
                $criteria[$field] = $request->attributes->get($attribute);
            }
        }

        return $this->filterNullCriteria($criteria, $configuration);
    }

    private function filterNullCriteria(array $criteria, ParamConverter $configuration): array
    {
        if ($this->getStripNullOption($configuration)) {
            return array_filter($criteria, static function ($value): bool {
                return null !== $value;
            });
        }

        return $criteria;
    }

    private function getStripNullOption(ParamConverter $configuration): bool
    {
        $options = $configuration->getOptions();

        if (isset($options['strip_null'])) {
            return (bool) $options['strip_null'];
        }

        return false;
    }

    private function isFieldSearchable(ClassMetadata $classMetadata, string $field): bool
    {
        return $classMetadata->hasField($field)
            || ($classMetadata->hasAssociation($field) && $classMetadata->isSingleValuedAssociation($field))
        ;
    }

    private function isFieldTranslatable(TranslatableClassMetadata $translatableMetadata, string $field): bool
    {
        foreach ($translatableMetadata->getTranslatableProperties() as $properties) {
            if (isset($properties[$field])) {
                return true;
            }
        }

        return false;
    }

    private function getRepository(ParamConverter $configuration): EntityRepository
    {
        return $this->getManager($configuration)->getRepository($configuration->getClass());
    }

    private function validateClass(ParamConverter $configuration): bool
    {
        return null !== $configuration->getClass();
    }

    private function validateManager(ParamConverter $configuration): bool
    {
        $manager = $this->getManager($configuration);

        if (null === $manager) {
            return false;
        }

        return !$manager->getMetadataFactory()->isTransient($configuration->getClass());
    }

    private function validateRepository(ParamConverter $configuration): bool
    {
        return $this->getRepository($configuration) instanceof TranslatableRepositoryInterface;
    }

    private function getDoctrineMetadata(ParamConverter $configuration): ClassMetadata
    {
        return $this->getManager($configuration)->getClassMetadata($configuration->getClass());
    }

    private function getTranslatableMetadata(ParamConverter $configuration): TranslatableClassMetadata
    {
        return $this->translatableListener->getExtendedMetadata(
            $this->getManager($configuration),
            $configuration->getClass()
        );
    }

    private function hasTranslatableProperties(ParamConverter $configuration): bool
    {
        return $this->getTranslatableMetadata($configuration)->hasTranslatableProperties();
    }
}
