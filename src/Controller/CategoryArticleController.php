<?php

namespace App\Controller;

use App\Entity\CategoryArticle;
use App\Form\CategoryArticleType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CategoryArticleRepository;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Contracts\Cache\CacheInterface;

class CategoryArticleController extends AbstractController
{
    /**
     * @Route("/admin/category/article", name="admin_category_article")
     */
    public function index(CategoryArticleRepository $repo): Response
    {
        return $this->render('backoffice/categoryArticle/index.html.twig', [
            'categories' => $repo->findAll(),
        ]);
    }

    /**
     * @Route("/admin/category/article_add", name="category_article_add")
     */

    public function addCategoryArticle(
        EntityManagerInterface $em,
        Request $req,
        CacheInterface $cache,
        FlashyNotifier $flashy
    ):Response{

        $categoryArticle = new CategoryArticle();
        $form = $this->createForm(CategoryArticleType::class, $categoryArticle);
        $form->add('imageFile', VichImageType::class,[
                'label'=>false,
                 'required'=>false,
                 'allow_delete'=>true,
                 'download_uri' => false,
                'image_uri' => true,
                "constraints"=>[
                    new NotNull()
                ]
        ]);
        $form->handleRequest($req);
        if($form->isSubmitted() and $form->isValid()){
            $em->persist($categoryArticle);
            $em->flush();
            $cache->delete("categorie-article-home");
            $cache->delete("categories-article-blog-by-categorie-page");
            $cache->delete("categories-article-blog-page");
            $flashy->success("Edited Successfully!");
            return $this->redirectToRoute('admin_category_article');
        }

        return $this->renderForm('backoffice/categoryArticle/add_category_article.html.twig',compact('form')    );
    }

    /**
     * Permet de supprimer une cathégorie d'article
     *
     * @param EntityManagerInterface $em
     * @return Response
     * @Route("/admin/category/article_delete/{slug}", name="category_article_delete")
     */
    public function categoryArticleDelete(
        EntityManagerInterface $em, 
        CategoryArticle $categoryArticle,
        CacheInterface $cache,
        FlashyNotifier $flashy
    ):Response{

        $em->remove($categoryArticle);
        $em->flush();
        $cache->delete("categorie-article-home");
        $cache->delete("categories-article-blog-by-categorie-page");
        $cache->delete("categories-article-blog-page");
        $flashy->success("Edited Successfully!");
        return $this->redirectToRoute('admin_category_article');
    }

    /**
     * @Route("/admin/category/article_edit/{slug}", name="category_article_edit")
     */

    public function categoryArticleEdit(
        EntityManagerInterface $em, 
        CategoryArticle $categoryArticle,
        Request $req,
        CacheInterface $cache,
        FlashyNotifier $flashy
    ):Response{

        $form = $this->createForm(CategoryArticleType::class, $categoryArticle);
        $form->add('imageFile', VichImageType::class,[
                'label'=>false,
                 'required'=>false,
                 'allow_delete'=>true,
                 'download_uri' => false,
                'image_uri' => true,
        ]);
        $form->handleRequest($req);
        if($form->isSubmitted() and $form->isValid()){
            $em->persist($categoryArticle);
            $em->flush();
            $cache->delete("categorie-article-home");
            $cache->delete("categories-article-blog-by-categorie-page");
            $cache->delete("categories-article-blog-page");
            $flashy->success("Edited Successfully!");
            return $this->redirectToRoute('admin_category_article');
        }

        return $this->renderForm('backoffice/categoryArticle/edit_category_article.html.twig',compact('form','categoryArticle'));
    }
}
