<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\Form\Type\FSi;

use FSi\Bundle\DoctrineExtensionsBundle\Form\EventListener\RemovableFileSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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
        $builder->add('file', 'file', array(
            'mapped' => true,
            'required' => $options['required'],
            'data_class' => 'FSi\DoctrineExtensions\Uploadable\File',
            'property_path' => $options['property_path'],
            'label' => false
        ));
        $builder->add('delete', 'checkbox', array(
            'compound' => false,
            'mapped' => false,
            'required' => false,
            'label' => $options['delete_label']
        ));

        $builder->addEventSubscriber($this->fileSubscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'compound' => true,
                'inherit_data' => true,
                'delete_label' => 'fsi_removable_file.delete'
            )
        );

        $resolver->setRequired(array(
            'property_path'
        ));
    }
}
