<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\DoctrineExtensionsBundle\ResourceType\FSi;

use FSi\Bundle\DoctrineExtensionsBundle\Form\EventListener\RemovableFileSubscriber;
use FSi\Bundle\ResourceRepositoryBundle\Repository\Resource\Type\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RemovableFileType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getResourceProperty()
    {
        return 'removableForm';
    }

    /**
     * Method should return form type used to modify resource.
     *
     * @return string
     */
    protected function getFormType()
    {
        return 'fsi_removable_file';
    }
}
