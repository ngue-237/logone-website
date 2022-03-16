<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/admin/article", name="article_list_admin")
     */
    public function index(ArticleRepository $articleRepo): Response
    {
        return $this->render('backoffice/article/article_list_admin.html.twig', [
            'articles' => $articleRepo->findAll(),
        ]);
    }

    /**
     * permet d'afficher les détail d'un article
     *
     * @param Article $article
     * @return Response
     * @Route("/blog/article/{slug}", name="article_detail")
     */
    public function articleDetail(Article $article):Response{
        return $this->render('frontoffice/blog_detail.html.twig', compact('article'));
    }

    /**
     * permet d'ajouter un article de blog
     *
     * @param Request $req
     * @param EntityManagerInterface $em
     * @return void
     * @Route("/admin/article_add", name="article_add", methods={"GET","POST"} )
     */
    public function addBlog(Request $req, EntityManagerInterface $em ){
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($req);

        if($form->isSubmitted() and $form->isValid()){
            $em->persist($article);
            $em->flush();
            return $this->redirectToRoute('article_list_admin');
        }

        return $this->renderForm("backoffice/article/article_add.html.twig", compact('form'));
    }

    /**
     * Undocumented function
     *
     * @param Request $req
     * @param EntityManagerInterface $em
     * @param Article $article
     * @return Response
     * @Route("/admin/article/article_edit/{slug}", name="article_edit")
     */
    public function editArticle(Request $req, EntityManagerInterface $em, Article $article):Response{
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($req);

        if($form->isSubmitted() and $form->isValid()){
            $em->flush();
            return $this->redirectToRoute('article_list_admin');
        }
        return $this->renderForm("backoffice/article/article_edit.html.twig", compact('form','article'));
    }

    /**
     * Undocumented function
     *
     * @param Article $article
     * @param Request $req
     * @param EntityManagerInterface $em
     * @return Response
     * @Route("/admin/article/article_delete/{slug}", name="article_delete")
     */
    public function articleDelete(Article $article, EntityManagerInterface $em):Response{
        $em->remove($article);
        $em->flush();

        return $this->redirectToRoute('article_list_admin');
    }
}
