<?php

namespace App\Form;

use App\Entity\ArticleBlog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType; 
use App\Entity\CategoryArticle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class ArticleBlogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('contenue',CKEditorType::class)
            ->add('imageFile',VichImageType::class)
            ->add('updatedAt')
            ->add('categoryArticle', EntityType::class, [
                // looks for choices from this entity
                'class' => CategoryArticle::class,
            
                // uses the User.username property as the visible option string
                'choice_label' => 'nom',
                'label' => 'Categorie' ,       
                // used to render a select box, check boxes or radios
                 //'multiple' => true,
                // 'expanded' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ArticleBlog::class,
        ]);
    }
}
