<?php

namespace App\Form;

use App\Entity\CategoryArticle;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CategoryArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,[
                "constraints"=>[
                    new NotNull(),
                    new Length([
                        'min' => 2,
                        "minMessage"=>"Your title must be at least {{ limit }} characters long",
                        "max" => 100,
                        "maxMessage"=>"Your title  cannot be longer than {{ limit }} characters",
                        ])
                ]
            ])
            ->add('content', CKEditorType::class,[
                "constraints"=>[
                    new NotNull(),
                    new Length([
                        'min' => 2,
                        "minMessage"=>"Your title must be at least {{ limit }} characters long",
                        ])
                ]
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CategoryArticle::class,
        ]);
    }
}
