<?php

namespace App\Controller;

use App\Entity\CategoryArticle;
use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\CategoryArticleRepository;
use App\Repository\CategoryServiceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog", methods={"GET"})
     */
    public function index(
        ArticleRepository $articleRepo, 
        CategoryArticleRepository $categoryArtRepo,
        PaginatorInterface $paginator,
        Request $req
        ): Response
    {
             $categories = $paginator->paginate(
                $categoryArtRepo->findAll(), 
            $req->query->getInt('page', 1), /*page number*/
            5/*limit per page*/
        );
        
        //      $articles = $paginator->paginate(
        //         $articleRepo->findAll(), 
        //     $req->query->getInt('page', 1), /*page number*/
        //     4/*limit per page*/
        // );
        return $this->render('frontoffice/blog.html.twig', [
            // 'articles' => $articles,
            'catgoriesArticle'=>$categories
        ]);
    }

    
    /**
     * Undocumented function
     * @Route("/blog/categories/{slug}", name="blog_by_categorie", methods={"GET"})
     * @param CategoryArticle $categoryArticle
     * @param ArticleRepository $articleRepo
     * @param CategoryArticleRepository $categoryArtRepo
     * @param PaginatorInterface $paginator
     * @param Request $req
     * @return Response
     */
    public function blog_by_categorie(CategoryArticle $categoryArticle,
        ArticleRepository $articleRepo,
        CategoryServiceRepository $categServiceRepo, 
        CategoryArticleRepository $categoryArtRepo,
        PaginatorInterface $paginator,
        Request $req
        ): Response
    {
        
        return $this->render('frontoffice/blog_by_categorie.html.twig', [
            'articles' => $paginator->paginate($articleRepo->findBy(['categoryArticle' => $categoryArticle,]), $req->query->getInt('page', 1),5),
            "articleOrderByView"=>$articleRepo->findAllByView(),
            "categoriesArticle" => $paginator->paginate($categoryArtRepo->findAll(), $req->query->getInt('page', 1), 3),
            "categoriesService"=>$paginator->paginate($categServiceRepo->findAll(), $req->query->getInt('page', 1), 6) ,
        ]);
    }
}
