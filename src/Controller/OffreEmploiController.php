<?php

namespace App\Controller;

use App\Entity\OffreEmploi;
use App\Form\OffreEmploiType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OffreEmploiRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OffreEmploiController extends AbstractController
{
    /**
     * @Route("/offre_emploi", name="app_offre_emploi")
     */
    public function index(): Response
    {
        return $this->render('offre_emploi/index.html.twig', [
            'controller_name' => 'OffreEmploiController',
        ]);
    }

    /**
     * @Route("/list_offre", name="offre_list")
     */
    public function readjob(Request $request, PaginatorInterface $pag, OffreEmploiRepository $rep)
    {
        $data = $rep->findAll();

        $jobs = $pag->paginate($data, $request->query->getInt('page', 1), 4);

        return $this->render('offre_emploi/manage_offres.html.twig', [
            'jobs' => $jobs
        ]);
    }
    
    /**
     * @Route("/addoffre", name="add_offre")
     */
    public function addoffer(Request $request, EntityManagerInterface $em)
    {
        $offre = new OffreEmploi();
        $form = $this->createForm(OffreEmploiType::class, $offre);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
               // $offre->setIdCandidat(null);
                $em->persist($offre);
                $em->flush();
                return $this->redirectToRoute('offre_list');
            } else {
                return $this->render('offre_emploi/post_offre.html.twig', [
                    'formOffre' => $form->createView(), 'message' => 'Check your fields !'
                ]);
            }
        }
        return $this->render('offre_emploi/post_offre.html.twig', [
            'formOffre' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete_offre/{id}", name="delete_offre")
     */
    public function deletejob($id, EntityManagerInterface $em)
    {
        $job = $em->getRepository(OffreEmploi::class)->find($id);
        $em->remove($job);
        $em->flush();
        return $this->redirectToRoute('offre_list');
    }

    /**
     * @Route("/edit_offre/{id}", name="edit_offre")
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
                return $this->redirectToRoute('offre_list');
            } else {
                return $this->render('offre_emploi/post_offre.html.twig', [
                    'formOffre' => $form->createView(), 'message' => 'Check your fields !'
                ]);
            }
        }
        return $this->render('offre_emploi/post_offre.html.twig', [
            'formOffre' => $form->createView(), 'message' => ''
        ]);
    }
}
