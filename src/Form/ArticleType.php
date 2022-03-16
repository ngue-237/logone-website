<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\CategoryArticle;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('categoryArticles', EntityType::class,[
                'mapped'=>false,
                'label'=>false,
                'required'=>false,
                'class'=>CategoryArticle::class,
                'choice_label'=>'title',
                'placeholder'=>'Choisir une cathégorie'
            ])
            ->add('content', CKEditorType::class, [
                'config' => array(
                'filebrowserBrowseRoute' => 'elfinder',
                'filebrowserBrowseRouteParameters' => array(
                'instance' => 'default',
                'homeFolder' => ''
            )
        ),
            ])
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
            'data_class' => Article::class,
        ]);
    }
}
