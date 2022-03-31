<?php

namespace App\Controller;

use App\services\CategoryServices;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(CategoryServices $categoryService, Request $req): Response
    {
        return $this->render('frontoffice/index.html.twig', [
           'categoriesService'=>$categoryService->getAllCategoryService($req)
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
     * @Route("/rendez-vous", name="app_rdv", methods={"GET"})
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
