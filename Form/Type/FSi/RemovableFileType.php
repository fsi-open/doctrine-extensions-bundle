<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DoctrineExtensionsBundle\Form\Type\FSi;

use FSi\Bundle\DoctrineExtensionsBundle\Form\EventListener\FileSubscriber;
use FSi\Bundle\DoctrineExtensionsBundle\Form\EventListener\RemovableFileSubscriber;
use FSi\Bundle\DoctrineExtensionsBundle\Form\Type\FSi\FileType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RemovableFileType extends AbstractType
{
    /**
     * @var RemovableFileSubscriber
     */
    private $fileSubscriber;

    public function __construct(RemovableFileSubscriber $fileSubscriber)
    {
        $this->fileSubscriber = $fileSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->isSymfony3() ? FormType::class : 'form';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'fsi_removable_file';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return $this->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $fileOptions = array_merge($this->getDefaultFileOptions(), $options['file_options']);
        $builder->add($builder->getName(), $options['file_type'], $fileOptions);

        $removeOptions = array_merge($this->getDefaultRemoveOptions(), $options['remove_options']);
        $builder->add($options['remove_name'], $options['remove_type'], $removeOptions);

        $this->removeFSiFileEventSubscriber($builder);
        $builder->addEventSubscriber($this->fileSubscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['label_attr']['for'] = $view[$form->getName()]->vars['id'];
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'compound' => true,
            'error_bubbling' => false,
            'inherit_data' => true,
            'required' => false,
            'remove_name' => 'remove',
            'remove_type' => $this->isSymfony3() ? CheckboxType::class : 'checkbox',
            'remove_options' => [],
            'file_type' => $this->isSymfony3() ? FileType::class : 'fsi_file',
            'file_options' => [],
        ]);

        $resolver->setAllowedTypes('remove_name', 'string');
        $resolver->setAllowedTypes('remove_type', 'string');
        $resolver->setAllowedTypes('remove_options', 'array');
        $resolver->setAllowedTypes('file_type', 'string');
        $resolver->setAllowedTypes('file_options', 'array');
    }

    private function removeFSiFileEventSubscriber(FormBuilderInterface $builder): void
    {
        foreach ($this->getFilePreSubmitListeners($builder) as $preSubmitListener) {
            if ($this->isFileSubscriber($preSubmitListener)) {
                $this->getFileEventDispatcher($builder)->removeSubscriber($preSubmitListener[0]);
            }
        }
    }

    private function getFilePreSubmitListeners(FormBuilderInterface $builder): array
    {
        return $this->getFileEventDispatcher($builder)->getListeners(FormEvents::PRE_SUBMIT);
    }

    private function getFileEventDispatcher(FormBuilderInterface $builder): EventDispatcherInterface
    {
        return $builder->get($builder->getName())->getEventDispatcher();
    }

    private function isFileSubscriber($listener): bool
    {
        return is_array($listener)
            && isset($listener[0])
            && $listener[0] instanceof FileSubscriber
        ;
    }

    private function getDefaultFileOptions(): array
    {
        return [
            'label' => false,
            'error_bubbling' => true
        ];
    }

    private function getDefaultRemoveOptions(): array
    {
        return [
            'required' => false,
            'label' => 'fsi_removable_file.remove',
            'mapped' => false,
            'translation_domain' => 'FSiDoctrineExtensionsBundle'
        ];
    }

    private function isSymfony3(): bool
    {
        return method_exists(AbstractType::class, 'getBlockPrefix');
    }
}
