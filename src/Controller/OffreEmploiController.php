<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Entity\OffreEmploi;
use App\Form\CandidatureType;
use App\Form\OffreEmploiType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OffreEmploiRepository;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\CategoryArticleRepository;
use App\Repository\CategoryServiceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OffreEmploiController extends AbstractController
{
    /**
     * @Route("/admin/jobslist", name="jobslist_back")
     */
    public function readjobsBack( OffreEmploiRepository $rep)
    {
        return $this->render('backoffice/offre_emploi/listoffresback.html.twig', [
            'jobs' => $rep->findAll()
        ]);
    }


    /**
     * @Route("/rejoingnez-nous", name="jobslist_front")
     */
    public function readjobsFront(Request $request, PaginatorInterface $pag, OffreEmploiRepository $rep)
    {
    
        $candidature = new Candidature();
        $form = $this->createForm(CandidatureType::class,$candidature);

        
        $cache = new FilesystemAdapter();
        $offres = $cache->get("jobs-join-us-page", function() use($rep, $request, $pag){
            return $pag->paginate($rep->findAll(), $request->query->getInt('page', 1), 4);
        });
      
        
        return $this->render('frontoffice/offre_emploi/listoffresfront.html.twig', [
            'list' => $offres,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/add_job", name="add_job")
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
                return $this->render('backoffice/offre_emploi/add_offre.html.twig', [
                    'form' => $form->createView(), 'message' => 'Check your fields !'
                ]);
            }
        }
        return $this->render('backoffice/offre_emploi/add_offre.html.twig', [
            'form' => $form->createView(), 'message'=> ''
        ]);
    }

    /**
     * @Route("/admin/delete_job/{id}", name="delete_job")
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
     * @Route("/offre-emploi/{slug}", name="jobdetails")
     */
    public function jobdetails(
        OffreEmploi $offreEmploi,
        OffreEmploiRepository $offreEmploiRepo,
        CategoryServiceRepository $catgServiceRepo,
        CategoryArticleRepository $catgArticleRepo,
        PaginatorInterface $paginator,
        Request $req,
        SeoPageInterface $seoPage
    ):Response
    {
        $candidature = new Candidature();
        $form = $this->createForm(CandidatureType::class,$candidature);
        
        $cache = new FilesystemAdapter();
        $job = $cache->get("offre-emploi-join-us-page".$offreEmploi->getSlug(), function() use($offreEmploi) {
            return $offreEmploi;
        });

        $allJobs = $cache->get("all-job-join-us-page", function() use($req, $paginator, $offreEmploiRepo){
            return $paginator->paginate($offreEmploiRepo->findAll(), $req->query->getInt('page', 1), 5);
        });
        
        $allCatgService = $cache->get("categorie-service", function() use($req, $paginator,$catgServiceRepo){
            return $paginator->paginate($catgServiceRepo->findAll(), $req->query->getInt('page', 1), 5);
        });

        $allCatgsArticle = $cache->get("categorie-article", function(ItemInterface $item) use($paginator, $req,$catgArticleRepo){
            return $paginator->paginate($catgArticleRepo->findAll(), $req->query->getInt('page', 1), 3);
        });

        $seoPage
                ->setTitle($job->getSlug())
                ->addMeta('name', 'description', $job->getDescription())
                ->addMeta('name', 'keywords', $job->getSlug())
                ->addMeta('property', 'og:title', $job->getSlug())
                ->addMeta('property', 'og:description', $job->getDescription())
            ;

        return $this->render('frontoffice/offre_emploi/jobdetails.html.twig', [
            'job' => $job,
            "allJobs"=>$allJobs,
             'form' => $form->createView(),
            "allCatgService"=>$allCatgService,
            "allCatgsArticle"=>$allCatgsArticle,
        ]);
    }

    /**
     * @Route("/admin/edit_offre/{id}", name="edit_job")
     */
    public function modifyjob(Request $request, OffreEmploi $job, EntityManagerInterface $em)
    {
        $form = $this->createForm(OffreEmploiType::class, $job);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em->persist($job);
                $em->flush();
                return $this->redirectToRoute('jobslist_back');
            } else {
                return $this->render('backoffice/offre_emploi/add_offre.html.twig', [
                    'form' => $form->createView(), 'message' => 'Check your fields !'
                ]);
            }
        }
        return $this->render('backoffice/offre_emploi/add_offre.html.twig', [
            'form' => $form->createView(), 'message' => ''
        ]);
    }


}
