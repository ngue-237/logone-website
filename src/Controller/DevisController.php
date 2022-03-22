<?php

namespace App\Controller;

use App\services\DevisService;
use App\Entity\Devis;
use App\Entity\CategoryService;
use App\Repository\DevisRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
         EntityManagerInterface $em, 
         ServiceRepository $serviceRepo,
         SerializerInterface $serializer, 
         DevisService $devisHelper
        ):Response{
        $devis = new Devis();
        $form = $this->createFormBuilder(['categories'=>$categoryService])
            -> add('lastname', TextType::class)
            ->add('firstname', TextType::class)
            ->add('email', EmailType::class)
            ->add('phoneNumber', TelType::class)
            ->add('company', TextType::class)
            ->add('country' )
            ->add('subject', TextareaType::class)
            ->add('categories', EntityType::class,[
                'class'=>CategoryService::class,
                'choice_label'=>'designation',
                'label'=>false
            ])
            ->getForm();

        $form->handleRequest($req);
        if($form->isSubmitted() and $form->isValid()){
            
            $devisSet = new Devis();
            $devisSet = $devisHelper->setDevis($devis, $form);
            
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
