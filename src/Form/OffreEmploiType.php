<?php

namespace App\Form;

use App\Entity\OffreEmploi;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class OffreEmploiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('nombrePoste', NumberType::class,[
                
            ])
            ->add('date_expiration', DateTimeType::class,[
                'widget' => 'single_text',
            ])
            ->add('location',TextType::class, ['attr' => ['id' => 'searchTextField','autocomplete'=>'on']
            ])
            ->add('description',CKEditorType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OffreEmploi::class,
        ]);
    }
}
