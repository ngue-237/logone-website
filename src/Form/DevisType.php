<?php

namespace App\Form;

use App\Entity\Devis;
use App\Entity\Service;
use App\Entity\CategoryService;
use App\Repository\ServiceRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use App\Repository\CategoryServiceRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class DevisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', TextType::class)
            ->add('firstname', TextType::class)
            ->add('email', EmailType::class)
            ->add('phoneNumber', TelType::class)
            ->add('company', TextType::class)
            ->add('country' )
            ->add('subject', TextareaType::class)
            
            // ->add('services', EntityType::class,[
            //     'mapped'=>false,
            //     'class'=>Service::class,
            //     'choice_label'=>'designation',
            //     'placeholder'=>'Choisir un service',
            //     'required'=>false,
            //     'label'=>false,
            //     'query_builder' =>function(ServiceRepository $servRep){
            //         return $servRep->createQueryBuilder('c')->orderBy('c.designation', 'ASC');
            //     }
            // ]) 
        ;

        // $formModifier = function (FormInterface $form, CategoryService $category= null) {
        //     $categories = null === $category ? [] : $category->getServices();

        //     //dd($cities);

        //     $form->add('services', EntityType::class, [
        //         'class'=>Service::class,
        //         'mapped'=>false,
        //         //'displayed' => false,
        //         'choice_label'=>'designation',
        //         'placeholder'=>'Choisir un service',
        //         'choices' => $categories,
        //         'required'=>false,
        //         'label'=>false,
        //         'query_builder' =>function(ServiceRepository $servRep){
        //             return $servRep->createQueryBuilder('c')->orderBy('c.designation', 'ASC');
        //         }
        //     ]);
        // };

        // $builder->addEventListener(
        //     FormEvents::PRE_SET_DATA,
        //     function (FormEvent $event) use ($formModifier) {
                
        //         $data = $event->getData();

        //         $formModifier($event->getForm(), $data->getCategories());
        //     }
        // );

        // $builder->get('categories')->addEventListener(
        //     FormEvents::POST_SUBMIT,
        //     function (FormEvent $event) use ($formModifier) {
        //         // It's important here to fetch $event->getForm()->getData(), as
        //         // $event->getData() will get you the client data (that is, the ID)
        //         $services = $event->getForm()->getData();

        //         // since we've added the listener to the child, we'll have to pass on
        //         // the parent to the callback functions!
        //         $formModifier($event->getForm()->getParent(), $services);
        //     }
        // );
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Devis::class,
        ]);
    }
}
