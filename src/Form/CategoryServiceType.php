<?php

namespace App\Form;

use App\Entity\CategoryService;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class CategoryServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('designation')
            ->add('description', CKEditorType::class)
            ->add('imageFile', VichImageType::class,[
                'label'=>false,
                 'required'=>false,
                 'allow_delete'=>true,
                 'download_uri' => false,
                'image_uri' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CategoryService::class,
        ]);
    }
}
