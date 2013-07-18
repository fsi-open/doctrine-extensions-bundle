<?php

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @author Norbert Orzechowicz <norbert@fsi.pl>
 */
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

    function it_should_add_tag_to_uploadable_listener_service(ContainerBuilder $builder,ParameterBagInterface $parameterBag, Definition $uploadable)
    {
        $builder->hasExtension(Argument::type('string'))->willReturn(false);
        $builder->addResource(Argument::type('\Symfony\Component\Config\Resource\FileResource'))->shouldBeCalled();
        $builder->setDefinition(Argument::type('string'), Argument::type('Symfony\Component\DependencyInjection\Definition'))->shouldBeCalled();
        $builder->getParameterBag()->shouldBeCalled()->willReturn($parameterBag);
        /* Above code is added only because builder is used in services loader */

        $builder->hasDefinition('fsi_doctrine_extensions.listener.uploadable')->shouldBeCalled()->willReturn(true);
        $builder->getDefinition('fsi_doctrine_extensions.listener.uploadable')->shouldBeCalled()->willReturn($uploadable);
        $uploadable->addMethodCall('setDefaultKeymaker', Argument::type('array'))->shouldBeCalled();
        $builder->setParameter('fsi_doctrine_extensions.default.filesystem.adapter.path', '%kernel.root_dir%/../web/uploaded')
            ->shouldBeCalled();
        $builder->setParameter('fsi_doctrine_extensions.listener.uploadable.configuration', array())->shouldBeCalled();
        $uploadable->addMethodCall('setDefaultFilesystem', Argument::type('array'))->shouldBeCalled();
        $uploadable->addTag('doctrine.event_subscriber', array('connection' => 'default'))->shouldBeCalled();

        $this->load(array(
            0 => array(
                'orm' => array(
                    'default' => array(
                        'uploadable' => true,
                    )
                )
            )
        ), $builder);
    }

    function it_should_add_uploadable_configuration_parameter_to_container(ContainerBuilder $builder,ParameterBagInterface $parameterBag, Definition $uploadable)
    {
        $builder->hasExtension(Argument::type('string'))->willReturn(false);
        $builder->addResource(Argument::type('\Symfony\Component\Config\Resource\FileResource'))->shouldBeCalled();
        $builder->setDefinition(Argument::type('string'), Argument::type('Symfony\Component\DependencyInjection\Definition'))->shouldBeCalled();
        $builder->getParameterBag()->shouldBeCalled()->willReturn($parameterBag);
        /* Above code is added only because builder is used in services loader */

        $builder->setParameter('fsi_doctrine_extensions.listener.uploadable.configuration', array(
            'FSi\Bundle\DemoBundle\Entity\Article' => array(
                'image' => array(
                    'filesystem' => 'some_filesystem',
                    'keymaker' => 'FSi\Bundle\DemoBundle\KeyMaker\SpecificKeyMaker'
                )
            )
        ))->shouldBeCalled();

        $this->load(array(
            0 => array(
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
