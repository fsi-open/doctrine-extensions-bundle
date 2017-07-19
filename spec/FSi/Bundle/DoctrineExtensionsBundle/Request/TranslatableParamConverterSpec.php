<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\Request;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use FSi\DoctrineExtensions\Translatable\Entity\Repository\TranslatableRepository;
use FSi\DoctrineExtensions\Translatable\Mapping\ClassMetadata as TranslatableMetadata;
use FSi\DoctrineExtensions\Translatable\TranslatableListener;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use stdClass;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TranslatableParamConverterSpec extends ObjectBehavior
{
    public function let(
        ManagerRegistry $managerRegistry,
        EntityManagerInterface $entityManager,
        ClassMetadataFactory $metadataFactory,
        ClassMetadata $classMetadata,
        TranslatableListener $translatableListener,
        TranslatableMetadata $translatableMetadata,
        EntityRepository $entityRepository,
        TranslatableRepository $translatableRepository,
        ParamConverter $paramConverter
    ) {
        $managerRegistry->getManagerForClass(Argument::type('string'))->willReturn($entityManager);
        $metadataFactory->isTransient(Argument::type('string'))->willReturn(false);
        $entityManager->getMetadataFactory()->willReturn($metadataFactory);
        $entityManager->getClassMetadata(Argument::type('string'))->willReturn($classMetadata);
        $entityManager->getRepository('NonTranslatableEntity')->willReturn($entityRepository);
        $entityManager->getRepository('TranslatableEntity')->willReturn($translatableRepository);
        $translatableListener->getExtendedMetadata(
            $entityManager,
            Argument::type('string'))->willReturn($translatableMetadata
        );
        $paramConverter->getOptions()->willReturn(array());
        $paramConverter->getName()->willReturn('object');
        $this->beConstructedWith($managerRegistry, $translatableListener);
    }

    public function it_does_not_support_non_translatable_classes(
        ParamConverter $paramConverter,
        TranslatableMetadata $translatableMetadata
    ) {
        $paramConverter->getOptions()->willReturn(array());
        $paramConverter->getClass()->willReturn('NonTranslatableEntity');
        $translatableMetadata->hasTranslatableProperties()->willReturn(false);

        $this->supports($paramConverter)->shouldReturn(false);
    }

    public function it_supports_translatable_classes(
        ParamConverter $paramConverter,
        TranslatableMetadata $translatableMetadata
    ) {
        $paramConverter->getOptions()->willReturn(array());
        $paramConverter->getClass()->willReturn('TranslatableEntity');
        $translatableMetadata->hasTranslatableProperties()->willReturn(true);

        $this->supports($paramConverter)->shouldReturn(true);
    }

    public function it_returns_false_from_apply_when_cannot_build_search_criteria(
        ParamConverter $paramConverter,
        TranslatableMetadata $translatableMetadata,
        Request $request,
        ParameterBag $attributes
    ) {
        $paramConverter->getClass()->willReturn('TranslatableEntity');
        $translatableMetadata->hasTranslatableProperties()->willReturn(true);

        $request->attributes = $attributes;
        $attributes->keys()->willReturn(array('parameter'));
        $translatableMetadata->getTranslatableProperties()->willReturn(array(
            'translations' => array(
                'translatableProperty' => 'translationField'
            )
        ));

        $this->apply($request, $paramConverter)->shouldReturn(false);
    }

    public function it_throws_404_when_object_is_not_found(
        ParamConverter $paramConverter,
        TranslatableMetadata $translatableMetadata,
        Request $request,
        ParameterBag $attributes,
        TranslatableRepository $translatableRepository
    ) {
        $paramConverter->getClass()->willReturn('TranslatableEntity');
        $translatableMetadata->hasTranslatableProperties()->willReturn(true);

        $request->getLocale()->willReturn('some_locale');
        $request->attributes = $attributes;
        $attributes->keys()->willReturn(array('translatableProperty'));
        $attributes->get('translatableProperty')->willReturn('translationValue');
        $translatableMetadata->getTranslatableProperties()->willReturn(array(
            'translations' => array(
                'translatableProperty' => 'translationField'
            )
        ));
        $translatableRepository->findTranslatableOneBy(
            array('translatableProperty' => 'translationValue'),
            null,
            'some_locale'
        )->willThrow('\Doctrine\ORM\NoResultException');

        $this->shouldThrow(new NotFoundHttpException('Object of class "TranslatableEntity" has not been found.'))
            ->during('apply', array($request, $paramConverter));
    }

    public function it_sets_found_object_as_request_parameter(
        ParamConverter $paramConverter,
        TranslatableMetadata $translatableMetadata,
        Request $request,
        ParameterBag $attributes,
        TranslatableRepository $translatableRepository,
        stdClass $object
    ) {
        $paramConverter->getClass()->willReturn('TranslatableEntity');
        $paramConverter->getName()->willReturn('object');
        $translatableMetadata->hasTranslatableProperties()->willReturn(true);

        $request->getLocale()->willReturn('some_locale');
        $request->attributes = $attributes;
        $attributes->keys()->willReturn(array('translatableProperty'));
        $attributes->get('translatableProperty')->willReturn('translationValue');
        $translatableMetadata->getTranslatableProperties()->willReturn(array(
            'translations' => array(
                'translatableProperty' => 'translationField'
            )
        ));
        $translatableRepository->findTranslatableOneBy(
            array('translatableProperty' => 'translationValue'),
            null,
            'some_locale'
        )->willReturn($object);

        $attributes->set('object', $object)->shouldBeCalled();

        $this->apply($request, $paramConverter);
    }
}
