<?php

namespace App\Form;

use App\Entity\Comments;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class,[
                "constraints" =>[
                    new NotNull(),
                    new Length([
                        "max"=>400,
                        "maxMessage"=>"Votre commentaires ne doit pas dépasser les {{ limit }} mots"
                    ])
                ]
            ])
            ->add('rgpd', CheckboxType::class,[
                'constraints'=> [new NotBlank()],
                'required'=>true,
            ])
            ->add("catcha", HiddenType::class, [
                "mapped"=>false,
                "constraints"=>[
                    new NotBlank(),
                    new NotNull()
                ]
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comments::class,
        ]);
    }
}
