<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('frontoffice/index.html.twig', [
            
        ]);
    }

    /**
     * @Route("/blog", name="blog")
     */
    public function blog(){
        return $this->render('frontoffice/blog.html.twig', [
            
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
     * @Route("/services", name="services")
     */
    public function service(){
        return $this->render('frontoffice/services.html.twig', [
            
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
