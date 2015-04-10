<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\Form\Type\FSi;

use FSi\Bundle\DoctrineExtensionsBundle\Form\EventListener\FileSubscriber;
use FSi\Bundle\DoctrineExtensionsBundle\Form\EventListener\RemovableFileSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class RemovableFileType extends AbstractType
{
    /**
     * @var RemovableFileSubscriber
     */
    private $fileSubscriber;

    /**
     * @param RemovableFileSubscriber $fileSubscriber
     */
    public function __construct(RemovableFileSubscriber $fileSubscriber)
    {
        $this->fileSubscriber = $fileSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'form';
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
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'compound' => true,
            'error_bubbling' => false,
            'inherit_data' => true,
            'remove_name' => 'remove',
            'remove_type' => 'checkbox',
            'remove_options' => array(),
            'file_type' => 'fsi_file',
            'file_options' => array(),
        ));

        $resolver->setAllowedTypes(array(
            'remove_name' => 'string',
            'remove_type' => 'string',
            'remove_options' => 'array',
            'file_type' => 'string',
            'file_options' => 'array'
        ));
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     */
    private function removeFSiFileEventSubscriber(FormBuilderInterface $builder)
    {
        foreach ($this->getFilePreSubmitListeners($builder) as $preSubmitListener) {
            if ($this->isFileSubscriber($preSubmitListener)) {
                $this->getFileEventDispatcher($builder)->removeSubscriber($preSubmitListener[0]);
            }
        }
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return array|callable[]
     */
    private function getFilePreSubmitListeners(FormBuilderInterface $builder)
    {
        return $this->getFileEventDispatcher($builder)
            ->getListeners(FormEvents::PRE_SUBMIT);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private function getFileEventDispatcher(FormBuilderInterface $builder)
    {
        return $builder->get($builder->getName())
            ->getEventDispatcher();
    }

    /**
     * @param callable $listener
     * @return boolean
     */
    private function isFileSubscriber($listener)
    {
        return is_array($listener) &&
            isset($listener[0]) &&
            ($listener[0] instanceof FileSubscriber);
    }

    /**
     * @return array
     */
    private function getDefaultFileOptions()
    {
        return array(
            'label' => false,
            'error_bubbling' => true
        );
    }

    /**
     * @return array
     */
    private function getDefaultRemoveOptions()
    {
        return array(
            'required' => false,
            'label' => 'fsi_removable_file.remove',
            'mapped' => false,
            'translation_domain' => 'FSiDoctrineExtensionsBundle'
        );
    }
}
