<?php

namespace App\Controller;

use App\Entity\CategoryService;
use App\Form\CategoryServiceType;
use App\Repository\CategoryServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/admin/add_category_service", name="category_service_add")
     */
    public function addCategoryService(EntityManagerInterface $em, Request $req):Response{
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
    public function modifierCategoryService($idCatg, EntityManagerInterface $em, CategoryServiceRepository $rep, Request $req):Response
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
        ]);
    }

}
