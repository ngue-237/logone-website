<?php

namespace App\Controller;

use DateInterval;
use App\Entity\Images;
use App\Entity\CategoryService;
use App\Form\CategoryServiceType;
use App\services\CategoryServices;
use App\services\ImageManagerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use App\Repository\CategoryServiceRepository;
use Symfony\Component\HttpFoundation\Request;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
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
     * @Route("/categories-services", name="categorie_service_all")
     */
    public function allCategoriesService(CategoryServices $categoryService, Request $req):Response{
        $cache = new FilesystemAdapter();

         $categoriesService = $cache->get("categorie-service-page", function(ItemInterface $item) use ($categoryService, $req){
             $item->expiresAfter(DateInterval::createFromDateString('3 hour'));   
            return $categoryService->getAllCategoryService($req);
        });

        return $this->render('frontoffice/category_services.html.twig', [
            'catgs' => $categoriesService,
        ]);
    }

    /**
     * @param $id
     * @param CategoryServiceRepository $rep
     * @param EntityManagerInterface $em
     * @return Response
     * @Route("/admin/delete_category_service/{slug}", name="category_service_delete")
     */
    public function deleteCategoryService(
        CategoryService $catg,
        EntityManagerInterface $em,
        FlashyNotifier $flashy
        ):Response
    {
        $em->remove($catg);
        $em->flush();
        $flashy->success("Deleted successfully","");
        return $this->redirectToRoute('category_service_list');
    }

    /**
     * @param EntityManagerInterface $em
     * @param Request $req
     * @return Response
     * @Route("/admin/add_category_service", name="category_service_add", methods={"GET","POST"})
     */
    public function addCategoryService(
        EntityManagerInterface $em, 
        Request $req,
        FlashyNotifier $flashy
    ):Response{
        $catg = new CategoryService();
        $form = $this->createForm(CategoryServiceType::class, $catg);
        $form->add('imageFile', VichImageType::class,[
                'label'=>false,
                 'required'=>false,
                 'allow_delete'=>true,
                 'download_uri' => false,
                 'image_uri' => true,
                 "constraints"=>[
                     new NotNull(),
                     new Image([
                         "maxSize" => "1024k"
                     ])
                 ]
                 ]);
        $form->handleRequest($req);
        if($form->isSubmitted() and $form->isValid()){
            $em->persist($catg);
            $em->flush();
            $flashy->success("Added successfully !","");
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
     * @Route("/admin/edit_category_service/{slug}", name="category_service_edit")
     */
    public function modifierCategoryService(
        CategoryService $catg, 
        EntityManagerInterface $em, 
        Request $req,
        FlashyNotifier $flashy
    ):Response
    {
        $form = $this->createForm(CategoryServiceType::class, $catg);
        $form->add('imageFile', VichImageType::class,[
                'label'=>false,
                 'required'=>false,
                 'allow_delete'=>true,
                 'download_uri' => false,
                 'image_uri' => true,
        ]);
        $form->handleRequest($req);
        if($form->isSubmitted() and $form->isValid()){
            $em->flush();
            $flashy->success("Edited successfully !","");
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
