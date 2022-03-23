<?php

namespace App\Controller;

use App\Entity\Devis;
use App\services\DevisService;
use App\Entity\CategoryService;
use App\services\MaillerService;
use App\Repository\DevisRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;

class DevisController extends AbstractController
{
    

    /**
     * permet générer un formulaire avec un sélect dynamique et ajouter un dévis
     *
     * @param Request $req
     * @param EntityManagerInterface $em
     * @return Response
     * @Route("/devis/{slug}", name="devis_add", methods={"GET" ,"POST"})
     */
    public function addDevis(
         CategoryService $categoryService,
         Request $req, 
         ServiceRepository $serviceRepo,
         DevisService $devisHelper,
         FlashyNotifier $flashy,
         MaillerService $mailer
        ):Response{
        $devis = new Devis();
        $form = $this->createFormBuilder(['categories'=>$categoryService])
            -> add('lastname', TextType::class,
            [   
            'constraints' => [new Length([
                'min' => 4,
                "minMessage"=>"Your lastname must be at least {{ limit }} characters long",
                "max" => 50,
                "maxMessage"=>"Your first name cannot be longer than {{ limit }} characters",
                ])],
            ])
            ->add('firstname', TextType::class,
            [
                
            'constraints' => [new Length([
                'min' => 4,
                "minMessage"=>"Your lastname must be at least {{ limit }} characters long",
                "max" => 50,
                "maxMessage"=>"Your first name cannot be longer than {{ limit }} characters",
                ])],
            ]
            )
            ->add('email', EmailType::class, [
                "constraints"=>[
                    new NotBlank(),
                    new Email([
                        "message" => "The email '{{ value }}' is not a valid email."
                    ])
                ]
            ])
            ->add('phoneNumber', TelType::class, [
               
                "constraints"=>[
                    new NotBlank(),
                    new Regex([
                        "pattern"=>"/^(\(0\))?[0-9]+$/",
                        "match"=>false,
                        "message"=>"your phonenumber is not correct"
                    ])
                ]
            ])
            ->add('company', TextType::class, [
                "constraints"=>[
                    new NotBlank()
                ]
            ])
            ->add('country',TextType::class, [
                "constraints"=>[
                    new NotBlank()
                ]
            ] )
            ->add('subject', TextareaType::class, [
                "constraints"=>[
                    new NotBlank(),
                    new Length([
                    'min' => 4,
                    "minMessage"=>"Your message must be at least {{ limit }} characters long",
                    "max" => 600,
                    "maxMessage"=>"Your message  cannot be longer than {{ limit }} characters",
                    ])
                ]
            ])
            ->add("rgpd", CheckboxType::class, [
                "constraints"=>[
                    new NotBlank()
                ]
            ])
            ->add('categories', EntityType::class,[
                'class'=>CategoryService::class,
                'choice_label'=>'designation',
                'label'=>false,
                "constraints"=>[
                    new NotBlank(),
                ]
            ])
            ->getForm();

        $form->handleRequest($req);
        if($form->isSubmitted() and $form->isValid()){
           
            
            $devisSet = new Devis();
            $devisSet = $devisHelper->setDevis($devis, $form);
            
            $flashy->success("Votre demande a été bien prise en compte vous serez recontactez dans les prochaines 24h!",'');

            return $this->redirectToRoute('home');
        }
        return $this->renderForm('frontoffice/devis_add.html.twig',compact(
            'form', 
            'categoryService'
        ));
    }

    /**
     * liste tout les devis qui sont dans la bd dans l'ordre décroissant
     *
     * @param DevisRepository $devisRepo
     * @return Response
     * @Route("/admin/devis_lists", name="devis_lists")
     */
    public function getAllDevis(DevisRepository $devisRepo):Response{
       
        return $this->render('backoffice/devis/devis_list.html.twig',[
            'devis'=>$devisRepo->findAllOderDesc()
        ]);
    }

    /**
     * permet de supprimer un devis
     *
     * @param DevisRepository $devisRepo
     * @param EntityManagerInterface $em
     * @return Response
     * @Route("/admin/devis_delete/{id}", name="devis_delete")
     */
    public function deleteDevis(Devis $devis, EntityManagerInterface $em, $id):Response{
        
        $em->remove($devis);
        $em->flush();
        return $this->redirectToRoute('devis_lists');
    }
}
