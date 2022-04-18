<?php

namespace App\Controller;

use DateInterval;
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
use MercurySeries\FlashyBundle\FlashyNotifier;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Cache\CacheInterface;

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
    public function readjobsFront(
        Request $request, 
        PaginatorInterface $pag, 
        OffreEmploiRepository $rep,
        CacheInterface $cache)
    {
    
        $candidature = new Candidature();
        $form = $this->createForm(CandidatureType::class,$candidature);

        
        $offres = $cache->get("jobs-join-us-page", function(ItemInterface $item) use($rep, $request, $pag){
            $item->expiresAfter(DateInterval::createFromDateString('3 hour')); 
            return $pag->paginate($rep->findAll(), $request->query->getInt('page', 1), 4);
        });
      
        
        return $this->render('frontoffice/offre_emploi/listoffresfront.html.twig', [
            'list' => $pag->paginate($rep->findAll(), $request->query->getInt('page', 1), 4),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/add_job", name="add_job")
     */
    public function addoffer(
        Request $request, 
        EntityManagerInterface $em,
        FlashyNotifier $flashy,
        CacheInterface $cache
        )
    {
        $offre = new OffreEmploi();
        $form = $this->createForm(OffreEmploiType::class, $offre);
        $form->add('imageFile', VichImageType::class,[
                'label'=>false,
                 'required'=>false,
                 'allow_delete'=>true,
                 'download_uri' => false,           
                'image_uri' => true,
                'delete_label' => 'Supprimez cette image',
                "constraints"=>[
                    new NotNull(),
                ]
                ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $em->persist($offre);
            $em->flush();
            $cache->delete("jobs-join-us-page");
            $flashy->success("Added successfully!");
            return $this->redirectToRoute('jobslist_back');
        }
        return $this->render('backoffice/offre_emploi/add_offre.html.twig', [
            'form' => $form->createView(), 'message'=> ''
        ]);
    }

    /**
     * @Route("/admin/delete_job/{id}", name="delete_job")
     */
    public function deletejob(
        $id,
     EntityManagerInterface $em,
    CacheInterface $cache
    )
    {
        $job = $em->getRepository(OffreEmploi::class)->find($id);
        
        $em->remove($job);
       
        $em->flush();
        $cache->delete("jobs-join-us-page");
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
        $job = $cache->get("offre-emploi-join-us-page".$offreEmploi->getSlug(), function(ItemInterface $item) use($offreEmploi) {
            $item->expiresAfter(DateInterval::createFromDateString('3 hour')); 
            return $offreEmploi;
        });

        $allJobs = $cache->get("all-job-join-us-page", function(ItemInterface $item) use($req, $paginator, $offreEmploiRepo){
            $item->expiresAfter(DateInterval::createFromDateString('3 hour')); 
            return $paginator->paginate($offreEmploiRepo->findAll(), $req->query->getInt('page', 1), 5);
        });
        
        $allCatgService = $cache->get("categorie-service", function(ItemInterface $item) use($req, $paginator,$catgServiceRepo){
            $item->expiresAfter(DateInterval::createFromDateString('3 hour')); 
            return $paginator->paginate($catgServiceRepo->findAll(), $req->query->getInt('page', 1), 5);
        });

        $allCatgsArticle = $cache->get("categorie-article", function(ItemInterface $item) use($paginator, $req,$catgArticleRepo){
            $item->expiresAfter(DateInterval::createFromDateString('3 hour')); 
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
    public function modifyjob(
        Request $request, 
        OffreEmploi $job, 
        EntityManagerInterface $em,
        CacheInterface $cache
        )
    {
        $form = $this->createForm(OffreEmploiType::class, $job);
        $form->add('imageFile', VichImageType::class,[
                'label'=>false,
                 'required'=>false,
                 'allow_delete'=>true,
                 'download_uri' => false,           
                'image_uri' => true,
                'delete_label' => 'Supprimez cette image',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em->persist($job);
                $em->flush();
                $cache->delete("jobs-join-us-page");
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
