<?php

namespace App\Controller;
use Symfony\Contracts\Cache\ItemInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\CategoryArticleRepository;
use App\Repository\CategoryServiceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class HomeController extends AbstractController
{
    
    /**
     * @Route("/", name="home")
     */
    public function index(
        Request $req, 
        PaginatorInterface $paginator,
        CategoryServiceRepository $catgServiceRepo,
        CategoryArticleRepository $categoryArtRepo
    ): Response
    {
        $cache = new FilesystemAdapter();
        
        $categoriesService = $cache->get("categorie-service", function(ItemInterface $item) use ($catgServiceRepo,$paginator, $req){
             $item->expiresAfter(2);   
            return $paginator->paginate($catgServiceRepo->findAllByDate(), $req->query->getInt('page', 1), 4);
        });

        $catgoriesArticle = $cache->get("categorie-article", function(ItemInterface $item) use($paginator, $req,$categoryArtRepo){
             $item->expiresAfter(2);
            return $paginator->paginate($categoryArtRepo->findAllByDate(), $req->query->getInt('page', 1), 3);
        });

        // $seoPage
        //         ->setTitle("")
        //         ->addMeta('property', 'og:title', "")
        //     ;

        return $this->render('frontoffice/index.html.twig', [
           'categoriesService'=>$paginator->paginate($catgServiceRepo->findAll(), $req->query->getInt('page', 1), 4) ,
           "catgoriesArticle" => $catgoriesArticle,
        ]);
    }

    /**
     * Undocumented function
     *@Route("/mentions-legales", name="app_mention_legale")
     * @return Response
     */
    public function mentionLegale():Response{
        return $this->render("frontoffice/mentions_legales.html.twig");
    }

    /**
     * @Route("/entreprise", name="about")
     */
    public function about(){
        return $this->render('frontoffice/about.html.twig', [   
        ]);
    }

    /**
     * Undocumented function
     *
     * @return Response
     * @Route("/prendre-rendez-vous", name="app_rdv", methods={"GET"})
     */
    public function rdv():Response{
        return $this->render("frontoffice/rdv.html.twig");
    }

    /**
     * @Route("/portfolio", name="portforlio")
     */
    public function portefolio(){
        return $this->render('frontoffice/portfolio.html.twig', [
            
        ]);
    }

    /**
     * @Route("/portfolio_detail", name="portforlio_details")
     */
    public function portefolioDetail(){
        return $this->render('frontoffice/portfolio-details.html.twig', [
            
        ]);
    }

}
