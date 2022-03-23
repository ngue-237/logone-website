<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Entity\OffreEmploi;
use App\Form\CandidatureType;
use App\Form\OffreEmploiType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OffreEmploiRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OffreEmploiController extends AbstractController
{
    /**
     * @Route("/jobslist", name="jobslist_back")
     */
    public function readjobsBack(Request $request, PaginatorInterface $pag, OffreEmploiRepository $rep)
    {
        return $this->render('frontoffice/offre_emploi/listoffresback.html.twig', [
            'jobs' => $rep->findAll()
        ]);
    }


    /**
     * @Route("/joblist", name="jobslist_front")
     */
    public function readjobsFront(Request $request, PaginatorInterface $pag, OffreEmploiRepository $rep)
    {
        $offres = $rep->findAll();
        $filtre = $request->get("searchaj");
        $candidature = new Candidature();
        $form = $this->createForm(CandidatureType::class,$candidature);
        $data = $rep->getdata($filtre);

        $jobs = $pag->paginate($data, $request->query->getInt('page', 1), 4);
        $nb = $rep->countday($filtre);
        //dd($offres);

        if ($request->get('ajax')) {
            return new JsonResponse([
                'content' => $this->renderView('frontoffice/offre_emploi/listoffresfront.html.twig', [
                    'list' => $jobs, 'nb' => $nb,
                    'form' => $form->createView()
                ])
            ]);
        }
        return $this->render('frontoffice/offre_emploi/listoffresfront.html.twig', [
            'list' => $offres,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/add_job", name="add_job")
     */
    public function addoffer(Request $request, EntityManagerInterface $em)
    {
        $offre = new OffreEmploi();
        $form = $this->createForm(OffreEmploiType::class, $offre);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
               // $offre->setIdCandidat(null);
                $offre->setDateDebut(new \DateTime('now'));
                $em->persist($offre);
                $em->flush();
                return $this->redirectToRoute('jobslist_back');
            } else {
                return $this->render('frontoffice/offre_emploi/add_offre.html.twig', [
                    'form' => $form->createView(), 'message' => 'Check your fields !'
                ]);
            }
        }
        return $this->render('frontoffice/offre_emploi/add_offre.html.twig', [
            'form' => $form->createView(), 'message'=> ''
        ]);
    }

    /**
     * @Route("/delete_job/{id}", name="delete_job")
     */
    public function deletejob($id, EntityManagerInterface $em)
    {
        $job = $em->getRepository(OffreEmploi::class)->find($id);
        
        $em->remove($job);
        dd($job);
        $em->flush();
        return $this->redirectToRoute('jobslist_back');
    }

    /**
     * @Route("/jobdetails/{id}", name="jobdetails")
     */
    public function jobdetails($id, EntityManagerInterface $em)
    {
        $job = $em->getRepository(OffreEmploi::class)->find($id);

        return $this->render('frontoffice/offre_emploi/jobdetails.html.twig', [
            'job' => $job,
        ]);
    }

    /**
     * @Route("/edit_offre/{id}", name="edit_job")
     */
    public function modifyjob(Request $request, $id)
    {
        $job = $this->getDoctrine()->getRepository(OffreEmploi::class)->find($id);
        $form = $this->createForm(OffreEmploiType::class, $job);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($job);
                $em->flush();
                return $this->redirectToRoute('jobslist_back');
            } else {
                return $this->render('frontoffice/offre_emploi/add_offre.html.twig', [
                    'form' => $form->createView(), 'message' => 'Check your fields !'
                ]);
            }
        }
        return $this->render('frontoffice/offre_emploi/add_offre.html.twig', [
            'form' => $form->createView(), 'message' => ''
        ]);
    }


}
