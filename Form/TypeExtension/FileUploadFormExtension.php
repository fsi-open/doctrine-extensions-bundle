<?php


namespace FSi\Bundle\DoctrineExtensionsBundle\Form\TypeExtension;


use FSi\Bundle\DoctrineExtensionsBundle\Form\FSiUploadableRequestHandler;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\AbstractType;
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

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return method_exists(AbstractType::class, 'getBlockPrefix')
            ? FormType::class
            : 'form';
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
        $resolver->setDefault('fsi_request_handler', true);
        $resolver->setRequired('fsi_request_handler');
        $resolver->setAllowedTypes('fsi_request_handler', 'bool');
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['fsi_request_handler']) {
            $builder->setRequestHandler($this->requestHandler);
        }
    }
}