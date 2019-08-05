<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\DoctrineExtensionsBundle\Form\TypeExtension;

use FSi\Bundle\DoctrineExtensionsBundle\Form\FSiUploadableRequestHandler;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FileUploadFormExtension extends AbstractTypeExtension
{
    /**
     * @var FSiUploadableRequestHandler
     */
    private $requestHandler;

    public function __construct(FSiUploadableRequestHandler $requestHandler)
    {
        $this->requestHandler = $requestHandler;
    }

    public static function getExtendedTypes()
    {
        return [FormType::class];
    }

    public function getExtendedType()
    {
        return method_exists(AbstractType::class, 'getBlockPrefix')
            ? FormType::class
            : 'form'
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('fsi_request_handler', true);
        $resolver->setRequired('fsi_request_handler');
        $resolver->setAllowedTypes('fsi_request_handler', 'bool');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['fsi_request_handler']) {
            $builder->setRequestHandler($this->requestHandler);
        }
    }
}
