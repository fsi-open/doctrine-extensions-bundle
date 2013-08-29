<?php

/**
 * (c) Fabryka Stron Internetowych sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\Form\Type\FSi;

use FSi\Bundle\DoctrineExtensionsBundle\Form\EventListener\FileSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FileType extends AbstractType
{
    /**
     * @return null|string|\Symfony\Component\Form\FormTypeInterface
     */
    public function getParent()
    {
        return 'file';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fsi_file';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new FileSubscriber());
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'required' => false,
            'data_class' => 'FSi\DoctrineExtensions\Uploadable\File'
        ));
    }
}