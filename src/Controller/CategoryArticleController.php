<?php

namespace App\Controller;

use App\Entity\CategoryArticle;
use App\Form\CategoryArticleType;
use App\services\ImageManagerService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CategoryArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
        Request $req
    ):Response{

        $categoryArticle = new CategoryArticle();
        $form = $this->createForm(CategoryArticleType::class, $categoryArticle);
        $form->handleRequest($req);
        if($form->isSubmitted() and $form->isValid()){
            $em->persist($categoryArticle);
            $em->flush();

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
        CategoryArticle $categoryArticle
        
    ):Response{

        $em->remove($categoryArticle);
        $em->flush();

        return $this->redirectToRoute('admin_category_article');
    }

    /**
     * @Route("/admin/category/article_edit/{slug}", name="category_article_edit")
     */

    public function categoryArticleEdit(
        EntityManagerInterface $em, 
        CategoryArticle $categoryArticle,
        Request $req,
        ImageManagerService $imageManager  
    ):Response{

        $form = $this->createForm(CategoryArticleType::class, $categoryArticle);
        $form->handleRequest($req);
        if($form->isSubmitted() and $form->isValid()){
            $em->persist($categoryArticle);
            $em->flush();

            return $this->redirectToRoute('admin_category_article');
        }

        return $this->renderForm('backoffice/categoryArticle/edit_category_article.html.twig',compact('form','categoryArticle'));
    }

    //  /**
    //  * @param Images $image
    //  * @Route("/admin/delete/images_category_service/{id}", name="category_article_delete_images", methods={"DELETE"})
    //  */
    // public function deleteImageCategory(
    //     Images $image,
    //     Request $req,
    //     EntityManagerInterface $em){
    //     $data = json_decode($req->getContent(), true);

    //     // On vérifie si le token est valide
    //     if($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])){
    //         // On récupère le nom de l'image
    //         $nom = $image->getName();
    //         // On supprime le fichier
    //         unlink($this->getParameter('images_directory').'/'.$nom);

    //         // On supprime l'entrée de la base
            
    //         $em->remove($image);
    //         $em->flush();

    //         // On répond en json
    //         return new JsonResponse(['success' => 1]);
    //     }else{
    //         return new JsonResponse(['error' => 'Token Invalide'], 400);
    //     }

    // }
}
