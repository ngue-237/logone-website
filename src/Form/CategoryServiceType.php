<?php

namespace App\Form;

use App\Entity\CategoryService;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CategoryServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('designation', TextType::class,[
                "constraints"=>[
                    new NotBlank(),
                    new Length([
                        'min' => 4,
                        "minMessage"=>"Your title must be at least {{ limit }} characters long",
                        "max" => 50,
                        "maxMessage"=>"Your title name cannot be longer than {{ limit }} characters",
                        ])
                ]
            ])
            ->add('description', CKEditorType::class,[
                "constraints"=>[
                    new NotBlank(),
                    new Length([
                        'min' => 50,
                        "minMessage"=>"Your content must be at least {{ limit }} characters long",
                        ])
                ]
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
