<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\CategoryArticle;
use App\Entity\CategoryService;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ArticleType extends AbstractType
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
            ->add('author', TextType::class,[
                "constraints"=>[
                    new NotNull(),
                    new Length([
                        'min' => 2,
                        "minMessage"=>"Your author name must be at least {{ limit }} characters long",
                        "max" => 100,
                        "maxMessage"=>"Your author name name cannot be longer than {{ limit }} characters",
                    ])
                ]
            ])
            ->add('categoryArticle', EntityType::class,[
                'label'=>false,
                'required'=>true,
                'class'=>CategoryArticle::class,
                'choice_label'=>'title',
                'placeholder'=>'Choisir une thématique',
                "constraints"=>[
                    new NotNull(),
                ]
            ])
            ->add('content', CKEditorType::class, [
                'config' => array(
                'filebrowserBrowseRoute' => 'elfinder',
                'filebrowserBrowseRouteParameters' => array(
                'instance' => 'default',
                'homeFolder' => '',
                "constraints"=>[
                    new NotNull(),
                ]
            )
        ),
            ])
            ->add('categoryService', EntityType::class,[
                'label'=>false,
                'required'=>true,
                'class'=>CategoryService::class,
                'choice_label'=>'designation',
                'placeholder'=>'Choisir un service correspondant',
                "constraints"=>[
                    new NotNull(),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
