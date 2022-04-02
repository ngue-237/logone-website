<?php

namespace App\Controller;

use Knp\Component\Pager\PaginatorInterface;
use App\Repository\CategoryArticleRepository;
use App\Repository\CategoryServiceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
        return $this->render('frontoffice/index.html.twig', [
           'categoriesService'=>$paginator->paginate($catgServiceRepo->findAll(), $req->query->getInt('page', 1), 4),
           "catgoriesArticle" => $paginator->paginate($categoryArtRepo->findAll(), $req->query->getInt('page', 1), 3),
        ]);
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
