<?php

namespace App\Controller;

use DateInterval;
use App\Entity\CategoryArticle;
use App\Repository\ArticleRepository;
use Symfony\Contracts\Cache\ItemInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\CategoryArticleRepository;
use App\Repository\CategoryServiceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Cache\CacheInterface;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog", methods={"GET"})
     */
    public function index(
        CategoryArticleRepository $categoryArtRepo,
        PaginatorInterface $paginator,
        Request $req, 
        CacheInterface $cache
        ): Response
    {
        
        $categoriesArticle = $cache->get("categories-article-blog-page",function(ItemInterface $item) use($categoryArtRepo, $req, $paginator){
            $item->expiresAfter(DateInterval::createFromDateString('1 hour'));   
            return $paginator->paginate($categoryArtRepo->findAllByDate(),$req->query->getInt('page', 1),3);
        });
        
        return $this->render('frontoffice/blog.html.twig', [
            'catgoriesArticle'=>$categoriesArticle,
        ]);
    }

    
    /**
     * Undocumented function
     * @Route("/blog/categories/{slug}", name="blog_by_categorie", methods={"GET"})
     * @param ArticleRepository $articleRepo
     * @param CategoryArticleRepository $categoryArtRepo
     * @param PaginatorInterface $paginator
     * @param Request $req
     * @return Response
     */
    public function blog_by_categorie(
        CategoryArticle $catgArticle,
        ArticleRepository $articleRepo,
        $slug,
        CategoryServiceRepository $categServiceRepo, 
        CategoryArticleRepository $categoryArtRepo,
        PaginatorInterface $paginator,
        Request $req,
        CacheInterface $cache
        ): Response
    {
       

        $articles = $cache->get("articles-blog-by-categorie-page-".$slug, function(ItemInterface $item) use($paginator, $articleRepo, $req,$slug,$catgArticle){
            $item->expiresAfter(DateInterval::createFromDateString('3 hour'));
            return $paginator->paginate($articleRepo->findAllByPublished($catgArticle->getId()), $req->query->getInt('page', 1),5);
        });
        $articleOrderByView = $cache->get("article-order-by-view-blog-by-categorie-page", function(ItemInterface $item) use($paginator, $articleRepo, $req){
            $item->expiresAfter(DateInterval::createFromDateString('3 hour'));
            return $paginator->paginate($articleRepo->findAllByView(), $req->query->getInt('page', 1),3);
        });
        $categoriesArticle = $cache->get("categories-article-blog-by-categorie-page", function(ItemInterface $item) use($paginator, $categoryArtRepo, $req){
            $item->expiresAfter(DateInterval::createFromDateString('3 hour'));
            return $paginator->paginate($categoryArtRepo->findAll(), $req->query->getInt('page', 1),3);
        });
        $categoriesService = $cache->get("categories-service-blog-by-categorie-page", function(ItemInterface $item) use($paginator, $categServiceRepo, $req){
            $item->expiresAfter(DateInterval::createFromDateString('3 hour'));
            return $paginator->paginate($categServiceRepo->findAll(), $req->query->getInt('page', 1),6);
        });
        
        return $this->render('frontoffice/blog_by_categorie.html.twig', [
            'articles' => $articles,
            "articleOrderByView"=>$articleOrderByView,
            "categoriesArticle" => $categoriesArticle,
            "categoriesService"=>$categoriesService,
        ]);
    }
}
