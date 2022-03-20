<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Form\CandidatureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CandidatureController extends AbstractController
{
    /**
     * @Route("/apply/{id}", name="apply")
     */
    public function addCandidature($id, Request $request, EntityManagerInterface $em)
    {
        $candidature = new Candidature();
        $offre = $em->getRepository(OffreEmploi::class)->find($id);
        $form = $this->createForm(CandidatureType::class,$candidature);

        if($form->isSubmitted() && $form->isValid()){
            $offre->addCandidature($candidature);
            $candidature->addOffreEmploi($offre);
            $candidature->setStatus(false);
            $em->persist($offre);
            $em->persist($candidature);
            $em->flush();
            return $this->redirectToRoute('joblist_front');
        }
        return $this->render('offre_emploi/listoffresfront.html.twig', [
            'form' => $form->createView(), 'message' => ''
        ]);
    }

}
