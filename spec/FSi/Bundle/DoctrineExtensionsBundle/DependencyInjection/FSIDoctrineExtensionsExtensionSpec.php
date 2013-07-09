<?php

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\DependencyInjection;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

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

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $builder
     * @param \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $parameterBag
     * @param \Symfony\Component\DependencyInjection\Definition $uploadable
     */
    function it_should_configure_service_container_builder($builder, $parameterBag, $uploadable)
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
        $uploadable->addMethodCall('setDefaultFilesystem', Argument::type('array'))->shouldBeCalled();
        $uploadable->addTag('doctrine.event_subscriber', array('connection' => 'default'))->shouldBeCalled();

        $this->load(array(
            0 => array(
                'orm' => array(
                    'default' => array(
                        'uploadable' => true
                    )
                )
            )
        ), $builder);
    }
}
