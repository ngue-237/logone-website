<?php

namespace App\Form;

use App\Entity\Service;
use App\Entity\CategoryService;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotNull;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('designation',TextType::class, [
                "constraints"=>[
                    new NotNull(),
                    new Length([
                        'min' => 2,
                        "minMessage"=>"Your title must be at least {{ limit }} characters long",
                        "max" => 34,
                        "maxMessage"=>"Your title name cannot be longer than {{ limit }} characters",
                        ])
                ]
            ])
            ->add('description', CKEditorType::class, [
                "constraints"=>[
                    new NotBlank(),
                    new Length([
                        'min' => 50,
                        "minMessage"=>"Your content must be at least {{ limit }} characters long",
                        ])
                ]
            ])
            ->add('category', EntityType::class,[
                'class'=>CategoryService::class,
                'choice_label'=>'designation',
                "placeholder"=>"choisir une cathégorie de sercice",
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
