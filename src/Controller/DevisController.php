<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Form\DevisType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DevisController extends AbstractController
{
    

    /**
     * @Route("/devis", name="devis_add", methods={"GET" ,"POST"})
     */
    public function addDevis(Request $req, EntityManagerInterface $em):Response{
        $devis = new Devis();
        $form = $this->createForm(DevisType::class, $devis);
        $form->handleRequest($req);
        if($form->isSubmitted() and $form->isValid()){
            $em->persist($devis);
            $em->flush();
            return $this->redirectToRoute('home');
        }
        return $this->render('frontoffice/devis_add.html.twig',[
            'form'=>$form->createView()
        ]);
    }
}
