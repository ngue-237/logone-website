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
use App\Repository\DevisRepository;
use App\Repository\ServiceRepository;
use Symfony\Component\HttpFoundation\Request;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Cache\CacheInterface;

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
    public function allCategoriesService(CategoryServices $categoryService, Request $req, CacheInterface $cache):Response{

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
        FlashyNotifier $flashy,
        CacheInterface $cache,
        ServiceRepository $serviceRepo,
        DevisRepository $devisRepo
        ):Response
    {
        try {
            if(count($serviceRepo->findByCategoryService($catg->getId())) == 0 || count($devisRepo->findAllDevisByCategoryService($catg->getId())) ){

            $em->remove($catg);
            $em->flush();
            $cache->delete("categorie-service");
            $cache->delete("categorie-service-page");
            $flashy->success("Deleted successfully","");
            return $this->redirectToRoute('category_service_list');
        }
        } catch (\Exception $e) {
            $flashy->error("impossible de supprimez ce catégorie car elle est ratachée à un devis ou à un service","");
            return $this->redirectToRoute('category_service_list');
        }
        // $flashy->error("impossible de supprimez ce catégorie car elle est ratachée à un devis ou à un service","");
        // return $this->redirectToRoute('category_service_list');
        
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
        FlashyNotifier $flashy,
        CacheInterface $cache
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
            $cache->delete("categorie-service");
            $cache->delete("categorie-service-page");
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
        FlashyNotifier $flashy,
        CacheInterface $cache
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
            $cache->delete("categorie-service");
            $cache->delete("categorie-service-page");
            return $this->redirectToRoute('category_service_list');
        }
        return $this->render('backoffice/category/modify_category_service.html.twig',[
            'form'=>$form->createView(),
            'categories'=>$catg
        ]);
    }

}
