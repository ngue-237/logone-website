<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName',TextType::class, ['label'=>false])
            ->add('firstName', TextType::class, ['label'=>false])
            ->add('email', TextType::class, ['label'=>false])
            ->add('company', TextType::class, ['label'=>false])
            ->add('phoneNumber', TelType::class, ['label'=>false])
            ->add('country', TextType::class, ['label'=>false])
            ->add('msg', TextareaType::class, ['label'=>false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
