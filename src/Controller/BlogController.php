<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\CategoryArticleRepository;
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
            3/*limit per page*/
        );
        
             $articles = $paginator->paginate(
                $articleRepo->findAll(), 
            $req->query->getInt('page', 1), /*page number*/
            5/*limit per page*/
        );
        return $this->render('frontoffice/blog.html.twig', [
            'articles' => $articles,
            'catgoriesArticle'=>$categories
        ]);
    }
}
