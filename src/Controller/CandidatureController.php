<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Entity\OffreEmploi;
use App\Form\CandidatureType;
use App\Repository\CandidatureRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OffreEmploiRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;

class CandidatureController extends AbstractController
{
    /**
     * @Route("/je-postule/{slug}", name="apply")
     */
    public function addCandidature(OffreEmploi $offre, Request $request, PaginatorInterface $pag, OffreEmploiRepository $rep,EntityManagerInterface $em)
    {
        $candidature = new Candidature();
      
        $form = $this->createForm(CandidatureType::class,$candidature);
        $data = $rep->findAll();
        $jobs = $pag->paginate($data, $request->query->getInt('page', 1), 4);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //dd($candidature,$request->getContent());
            $offre->addCandidature($candidature);
            $candidature->addOffreEmploi($offre);
            $candidature->setStatus(false);
            $em->persist($offre);
            $em->persist($candidature);
            $em->flush();
            return $this->redirectToRoute('jobslist_front',['list'=>$jobs]);
        }else{
            return $this->render('frontoffice/candidature/add_candidature.html.twig', [
                'form' => $form->createView(), 'message' => 'Check fields'
            ]);         
        }
        return $this->render('frontoffice/candidature/add_candidature.html.twig', [
            'form' => $form->createView(), 'message' => ''
        ]);
    }
    /**
     * @Route("/admin/delete_candidat/{id}", name="delete_candidat")
     */
    public function deleteCandidat($id, EntityManagerInterface $em)
    {
        $candidature = $em->getRepository(Candidature::class)->find($id);
        $em->remove($candidature);
        $em->flush();
        
        return $this->redirectToRoute('jobslist_back');
    }

    /**
     * @Route("/admin/offres_candidatures/{id}", name="candidats")
     */
    public function listCandidatsForAJob($id, OffreEmploiRepository $rep,EntityManagerInterface $em){
        $offre = $em->getRepository(OffreEmploi::class)->find($id);
        $canditures = $offre->getCandidatures();
        
        return $this->render('backoffice/candidature/candidatures.html.twig', [
            'canditures' => $canditures,
            'idOffre'=>$id
        ]);
        
    }
    /**
     * @Route("/admin/valid_candidat/{idCandidat}/{idOffre}", name="valid_candidat")
     */
    public function valid_candidat($idCandidat, $idOffre, EntityManagerInterface $em){
        
        $offre = $em->getRepository(OffreEmploi::class)->find(intval($idOffre));
        $candidat = $em->getRepository(Candidature::class)->find(intval($idCandidat));
        $offre->setNombrePoste($offre->getNombrePoste() - 1);
        $candidat->setStatus(true);
        $em->persist($offre);
        $em->persist($candidat);
        $em->flush();

        $canditures = $offre->getCandidatures();
        return $this->redirectToRoute('backoffice/candidature/candidatures.html.twig', [
            'canditures' => $canditures,
            'idOffre'=>$idOffre
        ]);

    }

    /**
     * @Route("/admin/listCandidatures", name="listCandidatures")
     */
    public function readCandidatsBack(Request $request, PaginatorInterface $pag, CandidatureRepository $rep)
    {

        return $this->render('backoffice/candidature/listCandidatures.html.twig', [
            'canditures' => $rep->findAll()
        ]);
    }
}
