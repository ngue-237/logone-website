<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Form\DevisType;
use App\Repository\DevisRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DevisController extends AbstractController
{
    

    /**
     * permet générer un formulaire avec un sélect dynamique et ajouter un dévis
     *
     * @param Request $req
     * @param EntityManagerInterface $em
     * @return Response
     * @Route("/devis", name="devis_add", methods={"GET" ,"POST"})
     */
    public function addDevis(Request $req, EntityManagerInterface $em, ServiceRepository $serviceRepo):Response{
        $devis = new Devis();
        $form = $this->createForm(DevisType::class, $devis);
        $form->handleRequest($req);
        if($form->isSubmitted() and $form->isValid()){
            $devis->setCreatedAt(new \DateTime());
            
            $devis->setServices($serviceRepo->find($form->get('services')->getData()->getId()));
            $em->persist($devis);
            $em->flush();
            return $this->redirectToRoute('home');
        }
        return $this->render('frontoffice/devis_add.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    /**
     * Undocumented function
     *
     * @param DevisRepository $devisRepo
     * @return Response
     * @Route("/admin/devis_lists", name="devis_lists")
     */
    public function getAllDevis(DevisRepository $devisRepo):Response{
        $devis = $devisRepo->findAll();
        return $this->render('backoffice/devis/devis_list.html.twig',[
            'devis'=>$devis
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
    public function deleteDevis(DevisRepository $devisRepo, EntityManagerInterface $em, $id):Response{
        $devis = $devisRepo->find($id);
        $em->remove($devis);
        $em->flush();
        return $this->redirectToRoute('devis_lists');
    }
}
