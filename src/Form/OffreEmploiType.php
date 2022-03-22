<?php

namespace App\Form;

use App\Entity\NiveauScolaire;
use App\Entity\OffreEmploi;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class OffreEmploiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre',TextType::class, ['label'=>false])
            ->add('description',TextareaType::class, ['label'=>false])
            ->add('location')
            ->add('file',FileType::class, array('data_class'=> null))
            ->add('nbPoste')
            ->add('nbAnneeExperience')
            ->add('niveaux', CollectionType::class, [
                'entry_type' => NiveauScolaireType::class,
                'label'=>'NiveauScolaire',
                'entry_options' => ['label' => false],
                'allow_add'=>true,
                'allow_delete'=>true,
                'by_reference'=>false
            ])
            ->add('Add', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OffreEmploi::class,
        ]);
    }
}
