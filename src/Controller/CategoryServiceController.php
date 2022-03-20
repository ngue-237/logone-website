<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\CategoryService;
use App\Form\CategoryServiceType;
use App\services\CategoryServices;
use App\services\ImageManagerService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CategoryServiceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryServiceController extends AbstractController
{
    /**
     * @Route("/admin/category_services", name="category_service_list")
     */
    public function index(CategoryServiceRepository $rep): Response
    {
        $catgs = $rep->findAll();

        return $this->render('backoffice/category/category_services_list.html.twig', [
            'catgs' => $catgs,
        ]);
    }

    /**
     * @param CategoryServiceRepository $rep
     * @return Response
     * @Route("/categories_service", name="categorie_service_all")
     */
    public function allCategoriesService(CategoryServices $categoryService, Request $req):Response{
        return $this->render('frontoffice/category_services.html.twig', [
            'catgs' => $categoryService->getAllCategoryService($req),
        ]);
    }



    /**
     * @param $id
     * @param CategoryServiceRepository $rep
     * @param EntityManagerInterface $em
     * @return Response
     * @Route("/admin/delete_category_service/{idCatg}", name="category_service_delete")
     */
    public function deleteCategoryService($idCatg, CategoryServiceRepository $rep, EntityManagerInterface $em):Response
    {
        $catg = $rep->find($idCatg);
        $em->remove($catg);
        $em->flush();
        return $this->redirectToRoute('category_service_list');
    }

    /**
     * @param EntityManagerInterface $em
     * @param Request $req
     * @return Response
     * @Route("/admin/add_category_service", name="category_service_add", methods={"GET","POST"})
     */
    public function addCategoryService(EntityManagerInterface $em, Request $req, ImageManagerService $imageManager):Response{
        $catg = new CategoryService();
        $form = $this->createForm(CategoryServiceType::class, $catg);
        $form->handleRequest($req);
        if($form->isSubmitted() and $form->isValid()){
            $em->persist($catg);
            $em->flush();
            return $this->redirectToRoute('category_service_list');
        }
        return $this->render('backoffice/category/add_category.html.twig',[
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @param $idCatg
     * @param EntityManagerInterface $em
     * @param CategoryServiceRepository $rep
     * @param Request $req
     * @return Response
     * @Route("/admin/edit_category_service/{idCatg}", name="category_service_edit")
     */
    public function modifierCategoryService(
        $idCatg, EntityManagerInterface $em, 
        CategoryServiceRepository $rep, 
        Request $req):Response
    {
        $catg = $rep->find($idCatg);
        $form = $this->createForm(CategoryServiceType::class, $catg);
        $form->handleRequest($req);
        if($form->isSubmitted() and $form->isValid()){
            $em->flush();
            return $this->redirectToRoute('category_service_list');
        }
        return $this->render('backoffice/category/modify_category_service.html.twig',[
            'form'=>$form->createView(),
            'categories'=>$catg
        ]);
    }

    /**
     * @param Images $image
     * @Route("/admin/delete/images_category_service/{id}", name="category_service_delete_images", methods={"DELETE"})
     */
    public function deleteImageCategory(Images $image, Request $req){
        $data = json_decode($req->getContent(), true);
        //dd($data);
        // On vérifie si le token est valide
        if($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])){
            // On récupère le nom de l'image
            $nom = $image->getName();
            // On supprime le fichier 
            unlink($this->getParameter('images_directory').'/'.$nom);

            // On supprime l'entrée de la base
            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();

            // On répond en json
            return new JsonResponse(['success' => 1]);
        }else{
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }

    }

}
