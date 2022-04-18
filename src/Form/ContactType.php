<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Url;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName',TextType::class, ['label'=>false])
            ->add('firstName', TextType::class, ['label'=>false])
            ->add('email', TextType::class, ['label'=>false])
            ->add('urlCustomWebsite', UrlType::class, ['label'=>false],
             [
                 "constraits"=>[
                    new NotBlank(),
                    new NotNull(),
                    new Url()
             ]
             ])
            ->add('msg', TextareaType::class, ['label'=>false])
            ->add('rgpd', CheckboxType::class, [
                "constraints"=>[
                    new NotBlank()
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
