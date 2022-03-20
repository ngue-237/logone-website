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
     * @Route("/blog/detail", name="blog_detail")
     */
    public function blogDetail(){
        return $this->render('frontoffice/blog_detail.html.twig', [
            
        ]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function about(){
        return $this->render('frontoffice/about.html.twig', [
            
        ]);
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
