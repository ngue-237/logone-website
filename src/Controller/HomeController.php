<?php

namespace App\Controller;

use App\Entity\ArticleBlog;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleBlogRepository;
use Symfony\Component\HttpFoundation\Request;

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
    public function blog(ArticleBlogRepository $articleBlogRepository){
        return $this->render('frontoffice/blog.html.twig', [
            'article_blogs' => $articleBlogRepository->findAll(),
        ]);
    }

    /**
     * @Route("/blog/detail/{id}", name="blog_detail",methods={"GET", "POST"})
     */
    public function blogDetail(ArticleBlog $articleBlog, Request $request): Response
    {

        $commentaires = $this->getDoctrine()->getRepository(Commentaire::class)->findBy([
            'article' => $articleBlog,
            'Actif' => 1
        ],['CreatedAt' => 'desc']);
        
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);
        dump($form->isSubmitted());
        //dump($request);
        if($form->isSubmitted() && $form->isValid()){
            $commentaire->setArticle($articleBlog);
            $commentaire->setCreatedAt(new \DateTime('now'));
            dump($request);
            $doctrine = $this->getDoctrine()->getManager();
            $doctrine->persist($commentaire);
            $doctrine->flush();
            $commentaires = $this->getDoctrine()->getRepository(Commentaire::class)->findBy([
                'article' => $articleBlog,
                'Actif' => 1
            ],['CreatedAt' => 'desc']);
        }
        return $this->render('frontoffice/blog_detail.html.twig', [
            'article' => $articleBlog,
            'commentaires' => $commentaires,
            'formComment' => $form->createView()
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
