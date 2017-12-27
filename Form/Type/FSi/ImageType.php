<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DoctrineExtensionsBundle\Form\Type\FSi;

use FSi\Bundle\DoctrineExtensionsBundle\Form\Type\FSi\FileType;
use FSi\Bundle\DoctrineExtensionsBundle\Validator\Constraints\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->isSymfony3() ? FileType::class : 'fsi_file';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'fsi_image';
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return $this->getName();
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
            'constraints' => [new Image()],
        ]);
    }

    /**
     * @return bool
     */
    private function isSymfony3(): bool
    {
        return method_exists(AbstractType::class, 'getBlockPrefix');
    }
}
