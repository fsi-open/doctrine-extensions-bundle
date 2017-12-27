<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\FSi\Bundle\DoctrineExtensionsBundle\Form\Type\FSi;

use FSi\Bundle\DoctrineExtensionsBundle\Form\EventListener\FileSubscriber;
use FSi\Bundle\DoctrineExtensionsBundle\Form\Type\FSi\FileType as FSiFileType;
use FSi\Bundle\DoctrineExtensionsBundle\Resolver\FSiFilePathResolver;
use FSi\Bundle\DoctrineExtensionsBundle\Validator\Constraints\File as FileConstraint;
use FSi\DoctrineExtensions\Uploadable\File;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FileTypeSpec extends ObjectBehavior
{
    function let(UrlGeneratorInterface $urlGenerator, FSiFilePathResolver $filePathResolver)
    {
        $this->beConstructedWith($urlGenerator, $filePathResolver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FSiFileType::class);
    }

    function it_should_have_valid_name()
    {
        $this->getName()->shouldReturn('fsi_file');
    }

    function it_should_be_child_of_form_type()
    {
        $this->getParent()->shouldReturn($this->isSymfony28() ? FileType::class : 'file');
    }

    function it_should_set_default_options(OptionsResolver $resolver)
    {
        $resolver->setDefaults(Argument::allOf(
            Argument::withEntry('data_class', File::class),
            Argument::withEntry(
                'constraints',
                Argument::withEntry(0, Argument::type(FileConstraint::class))
            )
        ))->shouldBeCalled();
        $resolver->setDefined('file_url')->shouldBeCalled();
        $resolver->setAllowedTypes('file_url', ['null', 'callable'])->shouldBeCalled();

        if ($this->isSymfony27()) {
            $this->configureOptions($resolver);
        } else {
            $this->setDefaultOptions($resolver);
        }
    }

    function it_should_register_listener(FormBuilderInterface $builder)
    {
        $builder->addEventSubscriber(Argument::type(FileSubscriber::class))->shouldBeCalled();

        $this->buildForm($builder, []);
    }

    private function isSymfony27(): bool
    {
        return method_exists(AbstractType::class, 'configureOptions');
    }

    private function isSymfony28(): bool
    {
        return method_exists(AbstractType::class, 'getBlockPrefix');
    }
}
