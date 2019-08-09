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
use FSi\Bundle\DoctrineExtensionsBundle\Resolver\FSiFilePathResolver;
use FSi\Bundle\DoctrineExtensionsBundle\Validator\Constraints\File;
use FSi\DoctrineExtensions\Uploadable\File as FSiFile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType as SymfonyFileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FileType extends AbstractType
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var FSiFilePathResolver
     */
    private $filePathResolver;

    public function __construct(UrlGeneratorInterface $urlGenerator, FSiFilePathResolver $filePathResolver)
    {
        $this->urlGenerator = $urlGenerator;
        $this->filePathResolver = $filePathResolver;
    }

    public function getParent()
    {
        return SymfonyFileType::class;
    }

    public function getBlockPrefix()
    {
        return 'fsi_file';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new FileSubscriber());
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (false === $form->getData() instanceof FSiFile) {
            return;
        }

        if (isset($options['file_url'])) {
            $fileUrlCallable = $options['file_url'];
            $view->vars['file_url'] = $fileUrlCallable($this->urlGenerator, $form);
        } else {
            $view->vars['file_url'] = $this->filePathResolver->fileUrl($form->getData());
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FSiFile::class,
            'constraints' => [new File()]
        ]);
        $resolver->setDefined('file_url');
        $resolver->setAllowedTypes('file_url', ['null', 'callable']);
    }
}
