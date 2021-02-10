<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection;

use Doctrine\Persistence\ManagerRegistry;
use FSi\Bundle\DemoBundle\Entity\Article;
use FSi\Bundle\DemoBundle\KeyMaker\SpecificKeyMaker;
use FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection\FSIDoctrineExtensionsExtension;
use FSi\Bundle\DoctrineExtensionsBundle\Request\TranslatableParamConverterNew;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use function interface_exists;

class FSIDoctrineExtensionsExtensionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(FSIDoctrineExtensionsExtension::class);
    }

    function it_should_have_a_valid_alias()
    {
        $this->getAlias()->shouldReturn('fsi_doctrine_extensions');
    }

    function it_should_add_tag_to_uploadable_listener_service(
        ContainerBuilder $builder,
        ParameterBagInterface $parameterBag,
        Definition $translatable,
        Definition $uploadable
    ) {
        if (true === interface_exists(ManagerRegistry::class)) {
            $builder->setParameter(
                'fsi_doctrine_extensions.request.translatable_param_converter.class',
                TranslatableParamConverterNew::class
            )->shouldBeCalled();
        }

        if (true === method_exists(ContainerBuilder::class, 'removeBindings')) {
            $builder->removeBindings(Argument::type('string'))->shouldBeCalled();
        }

        $builder->hasExtension(Argument::type('string'))->willReturn(false);
        if (method_exists(ContainerBuilder::class, 'fileExists')) {
            $builder->fileExists(Argument::type('string'))->willReturn(true);
        } else {
            $builder->addResource(Argument::type(FileResource::class))->shouldBeCalled();
        }
        $builder->setDefinition(Argument::type('string'), Argument::type(Definition::class))->shouldBeCalled();
        $builder->getParameterBag()->willReturn($parameterBag);
        /* Above code is added only because builder is used in services loader. */

        $builder->findTaggedServiceIds('fsi_doctrine_extensions.listener.uploadable')
            ->willReturn(['fsi_doctrine_extensions.listener.uploadable' => []]);

        $builder->getDefinition('fsi_doctrine_extensions.listener.translatable')->willReturn($translatable);

        $builder->getDefinition('fsi_doctrine_extensions.listener.uploadable')->willReturn($uploadable);

        $uploadable->addMethodCall('setDefaultKeymaker', Argument::type('array'))->shouldBeCalled();
        $builder->setParameter(
            'fsi_doctrine_extensions.default.filesystem.adapter.path',
            '%kernel.root_dir%/../web/uploaded'
        )->shouldBeCalled();

        $uploadable->addMethodCall('setDefaultKeymaker', Argument::type('array'))->shouldBeCalled();

        $uploadable->replaceArgument(2, [])->shouldBeCalled();

        $builder->setParameter('fsi_doctrine_extensions.default.filesystem.base_url', '/uploaded')
            ->shouldBeCalled()
        ;

        $uploadable->getTag("fsi_doctrine_extensions.listener.uploadable")->willReturn([['priority' => 0]]);
        $uploadable->addMethodCall('setDefaultFilesystem', Argument::type('array'))->shouldBeCalled();
        $uploadable->addTag(
            'doctrine.event_subscriber',
            ['connection' => 'default', 'priority' => 0]
        )->shouldBeCalled();

        $this->load([[
            'default_locale' => 'pl', 'orm' => ['default' => ['uploadable' => true]]
        ]], $builder);
    }

    function it_should_add_tag_to_translatable_listener_service(
        ContainerBuilder $builder,
        ParameterBagInterface $parameterBag,
        Definition $translatable,
        Definition $uploadable
    ) {
        if (true === interface_exists(ManagerRegistry::class)) {
            $builder->setParameter(
                'fsi_doctrine_extensions.request.translatable_param_converter.class',
                TranslatableParamConverterNew::class
            )->shouldBeCalled();
        }

        if (true === method_exists(ContainerBuilder::class, 'removeBindings')) {
            $builder->removeBindings(Argument::type('string'))->shouldBeCalled();
        }

        $builder->hasExtension(Argument::type('string'))->willReturn(false);
        if (method_exists(ContainerBuilder::class, 'fileExists')) {
            $builder->fileExists(Argument::type('string'))->willReturn(true);
        } else {
            $builder->addResource(Argument::type(FileResource::class))->shouldBeCalled();
        }
        $builder->setDefinition(Argument::type('string'), Argument::type(Definition::class))->shouldBeCalled();
        $builder->getParameterBag()->willReturn($parameterBag);
        /* Above code is added only because builder is used in services loader. */

        $builder->findTaggedServiceIds('fsi_doctrine_extensions.listener.translatable')
            ->willReturn(['fsi_doctrine_extensions.listener.translatable' => []]);

        $builder->getDefinition('fsi_doctrine_extensions.listener.uploadable')
            ->willReturn($uploadable);

        $builder->setParameter('fsi_doctrine_extensions.default.filesystem.adapter.path',
            '%kernel.root_dir%/../web/uploaded')->shouldBeCalled();

        $uploadable->addMethodCall('setDefaultFilesystem', Argument::type('array'))->shouldBeCalled();

        $uploadable->addMethodCall('setDefaultKeymaker', Argument::type('array'))->shouldBeCalled();

        $uploadable->replaceArgument(2, [])->shouldBeCalled();

        $builder->setParameter('fsi_doctrine_extensions.default.filesystem.base_url', '/uploaded')
            ->shouldBeCalled();

        $translatable->getTag("fsi_doctrine_extensions.listener.translatable")->willReturn([['priority' => 1]]);
        $builder->getDefinition('fsi_doctrine_extensions.listener.translatable')->willReturn($translatable);
        $translatable->addTag(
            'doctrine.event_subscriber',
            ['connection' => 'default', 'priority' => 1]
        )->shouldBeCalled();
        $translatable->addMethodCall('setDefaultLocale', ['pl'])->shouldBeCalled();

        $this->load([[
            'default_locale' => 'pl',
            'orm' => ['default' => ['translatable' => true]]
        ]], $builder);
    }

    function it_should_add_uploadable_configuration_parameter_to_container(
        ContainerBuilder $builder,
        ParameterBagInterface $parameterBag,
        Definition $translatable,
        Definition $uploadable
    ) {
        if (true === interface_exists(ManagerRegistry::class)) {
            $builder->setParameter(
                'fsi_doctrine_extensions.request.translatable_param_converter.class',
                TranslatableParamConverterNew::class
            )->shouldBeCalled();
        }

        if (true === method_exists(ContainerBuilder::class, 'removeBindings')) {
            $builder->removeBindings(Argument::type('string'))->shouldBeCalled();
        }

        $builder->hasExtension(Argument::type('string'))->willReturn(false);
        if (method_exists(ContainerBuilder::class, 'fileExists')) {
            $builder->fileExists(Argument::type('string'))->willReturn(true);
        } else {
            $builder->addResource(Argument::type(FileResource::class))->shouldBeCalled();
        }

        $builder->setDefinition(Argument::type('string'), Argument::type(Definition::class))->shouldBeCalled();
        $builder->getParameterBag()->shouldBeCalled()->willReturn($parameterBag);
        /* Above code is added only because builder is used in services loader. */

        $builder->getDefinition('fsi_doctrine_extensions.listener.translatable')->shouldBeCalled()->willReturn($translatable);

        $builder->getDefinition('fsi_doctrine_extensions.listener.uploadable')
            ->shouldBeCalled()
            ->willReturn($uploadable);

        $builder->setParameter('fsi_doctrine_extensions.default.filesystem.adapter.path',
            '%kernel.root_dir%/../web/uploaded')->shouldBeCalled();

        $builder->setParameter('fsi_doctrine_extensions.default.filesystem.base_url', '/uploaded')
            ->shouldBeCalled();

        $uploadable->addMethodCall('setDefaultFilesystem', Argument::type('array'))->shouldBeCalled();

        $uploadable->addMethodCall('setDefaultKeymaker', Argument::type('array'))->shouldBeCalled();

        $uploadable->replaceArgument(2, [
            Article::class => [
                'image' => [
                    'filesystem' => 'some_filesystem',
                    'keymaker' => SpecificKeyMaker::class
                ]
            ]
        ])->shouldBeCalled();

        $this->load([[
            'default_locale' => 'pl',
            'uploadable_configuration' => [
                Article::class => [
                    'configuration' => [
                        'image' => [
                            'filesystem' => 'some_filesystem',
                            'keymaker' => SpecificKeyMaker::class
                        ]
                    ]
                ]
            ],
        ]], $builder);
    }
}
