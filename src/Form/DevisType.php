<?php

namespace App\Form;

use App\Entity\CategoryService;
use App\Entity\Devis;
use App\Entity\Service;
use App\Repository\CategoryServiceRepository;
use App\Repository\ServiceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DevisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname')
            ->add('firstname')
            ->add('email')
            ->add('phoneNumber')
            ->add('company')
            ->add('country')
            ->add('subject')
            ->add('categories', EntityType::class,[
                'class'=>CategoryService::class,
                'choice_label'=>'designation',
                'placeholder'=>'Choisir la Cathégorie du service',
                'label'=>false,
                'query_builder' =>function(CategoryServiceRepository $catgRep){
                    return $catgRep->createQueryBuilder('c')->orderBy('c.designation', 'ASC');
                }
            ])
            ->add('services', EntityType::class,[
                'mapped'=>false,
                'class'=>Service::class,
                'choice_label'=>'designation',
                'placeholder'=>'Choisir un service',
                'label'=>false,
                'query_builder' =>function(ServiceRepository $servRep){
                    return $servRep->createQueryBuilder('c')->orderBy('c.designation', 'ASC');
                }
            ])
            
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Devis::class,
        ]);
    }
}
