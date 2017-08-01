<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FSIDoctrineExtensionsExtensionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection\FSIDoctrineExtensionsExtension');
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
        $builder->hasExtension(Argument::type('string'))->willReturn(false);
        if (method_exists('Symfony\Component\DependencyInjection\ContainerBuilder', 'fileExists')) {
            $builder->fileExists(Argument::type('string'))->willReturn(true);
        } else {
            $builder->addResource(Argument::type('\Symfony\Component\Config\Resource\FileResource'))->shouldBeCalled();
        }
        $builder->setDefinition(Argument::type('string'), Argument::type('Symfony\Component\DependencyInjection\Definition'))->shouldBeCalled();
        $builder->getParameterBag()->willReturn($parameterBag);
        /* Above code is added only because builder is used in services loader. */

        $builder->findTaggedServiceIds('fsi_doctrine_extensions.listener.uploadable')
            ->willReturn(array('fsi_doctrine_extensions.listener.uploadable' => array()));

        $builder->getDefinition('fsi_doctrine_extensions.listener.translatable')->willReturn($translatable);

        $builder->getDefinition('fsi_doctrine_extensions.listener.uploadable')->willReturn($uploadable);

        $uploadable->addMethodCall('setDefaultKeymaker', Argument::type('array'))->shouldBeCalled();
        $builder->setParameter('fsi_doctrine_extensions.default.filesystem.adapter.path', '%kernel.root_dir%/../web/uploaded')
            ->shouldBeCalled();
        $builder->setParameter('fsi_doctrine_extensions.listener.uploadable.configuration', array())->shouldBeCalled();
        $builder->setParameter('fsi_doctrine_extensions.default.filesystem.adapter.prefix', 'uploaded')
            ->shouldBeCalled();
        $builder->setParameter('fsi_doctrine_extensions.default.filesystem.base_url', '/uploaded')
            ->shouldBeCalled();

        $tag = array(array('priority' => 0));
        $uploadable->getTag("fsi_doctrine_extensions.listener.uploadable")->willReturn($tag);
        $uploadable->addMethodCall('setDefaultFilesystem', Argument::type('array'))->shouldBeCalled();
        $uploadable->addTag(
            'doctrine.event_subscriber',
            array('connection' => 'default', 'priority' => 0)
        )->shouldBeCalled();

        $this->load(array(
            0 => array(
                'default_locale' => 'pl',
                'orm' => array(
                    'default' => array(
                        'uploadable' => true,
                    )
                )
            )
        ), $builder);
    }

    function it_should_add_tag_to_translatable_listener_service(
        ContainerBuilder $builder,
        ParameterBagInterface $parameterBag,
        Definition $translatable,
        Definition $uploadable
    ) {
        $builder->hasExtension(Argument::type('string'))->willReturn(false);
        if (method_exists('Symfony\Component\DependencyInjection\ContainerBuilder', 'fileExists')) {
            $builder->fileExists(Argument::type('string'))->willReturn(true);
        } else {
            $builder->addResource(Argument::type('\Symfony\Component\Config\Resource\FileResource'))->shouldBeCalled();
        }
        $builder->setDefinition(Argument::type('string'), Argument::type('Symfony\Component\DependencyInjection\Definition'))->shouldBeCalled();
        $builder->getParameterBag()->willReturn($parameterBag);
        /* Above code is added only because builder is used in services loader. */

        $builder->findTaggedServiceIds('fsi_doctrine_extensions.listener.translatable')
            ->willReturn(array('fsi_doctrine_extensions.listener.translatable' => array()));

        $builder->getDefinition('fsi_doctrine_extensions.listener.uploadable')
            ->willReturn($uploadable);

        $builder->setParameter('fsi_doctrine_extensions.default.filesystem.adapter.path',
            '%kernel.root_dir%/../web/uploaded')->shouldBeCalled();

        $builder->setParameter('fsi_doctrine_extensions.listener.uploadable.configuration', array())
            ->shouldBeCalled();

        $builder->setParameter('fsi_doctrine_extensions.default.filesystem.adapter.prefix', 'uploaded')
            ->shouldBeCalled();
        $builder->setParameter('fsi_doctrine_extensions.default.filesystem.base_url', '/uploaded')
            ->shouldBeCalled();

        $tag = array(array('priority' => 1));
        $translatable->getTag("fsi_doctrine_extensions.listener.translatable")->willReturn($tag);
        $builder->getDefinition('fsi_doctrine_extensions.listener.translatable')->willReturn($translatable);
        $translatable->addTag(
            'doctrine.event_subscriber',
            array('connection' => 'default', 'priority' => 1)
        )->shouldBeCalled();
        $translatable->addMethodCall('setDefaultLocale', array('pl'))->shouldBeCalled();

        $this->load(array(
            0 => array(
                'default_locale' => 'pl',
                'orm' => array(
                    'default' => array(
                        'translatable' => true,
                    )
                )
            )
        ), $builder);
    }

    function it_should_add_uploadable_configuration_parameter_to_container(
        ContainerBuilder $builder,
        ParameterBagInterface $parameterBag,
        Definition $translatable,
        Definition $uploadable
    ) {
        $builder->hasExtension(Argument::type('string'))->willReturn(false);
        if (method_exists('Symfony\Component\DependencyInjection\ContainerBuilder', 'fileExists')) {
            $builder->fileExists(Argument::type('string'))->willReturn(true);
        } else {
            $builder->addResource(Argument::type('\Symfony\Component\Config\Resource\FileResource'))->shouldBeCalled();
        }

        $builder->setDefinition(Argument::type('string'), Argument::type('Symfony\Component\DependencyInjection\Definition'))->shouldBeCalled();
        $builder->getParameterBag()->shouldBeCalled()->willReturn($parameterBag);
        /* Above code is added only because builder is used in services loader. */

        $builder->getDefinition('fsi_doctrine_extensions.listener.translatable')->shouldBeCalled()->willReturn($translatable);

        $builder->getDefinition('fsi_doctrine_extensions.listener.uploadable')
            ->shouldBeCalled()
            ->willReturn($uploadable);

        $builder->setParameter('fsi_doctrine_extensions.default.filesystem.adapter.path',
            '%kernel.root_dir%/../web/uploaded')->shouldBeCalled();

        $builder->setParameter('fsi_doctrine_extensions.listener.uploadable.configuration', array(
            'FSi\Bundle\DemoBundle\Entity\Article' => array(
                'image' => array(
                    'filesystem' => 'some_filesystem',
                    'keymaker' => 'FSi\Bundle\DemoBundle\KeyMaker\SpecificKeyMaker'
                )
            )
        ))->shouldBeCalled();

        $builder->setParameter('fsi_doctrine_extensions.default.filesystem.adapter.prefix', 'uploaded')
            ->shouldBeCalled();
        $builder->setParameter('fsi_doctrine_extensions.default.filesystem.base_url', '/uploaded')
            ->shouldBeCalled();

        $this->load(array(
            0 => array(
                'default_locale' => 'pl',
                'uploadable_configuration' => array(
                    'FSi\Bundle\DemoBundle\Entity\Article' => array(
                        'configuration' => array(
                            'image' => array(
                                'filesystem' => 'some_filesystem',
                                'keymaker' => 'FSi\Bundle\DemoBundle\KeyMaker\SpecificKeyMaker'
                            )
                        )
                    )
                ),
            )
        ), $builder);
    }
}
