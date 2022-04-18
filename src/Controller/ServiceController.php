<?php

namespace App\Controller;

use DateInterval;
use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Contracts\Cache\ItemInterface;
use App\Repository\CategoryServiceRepository;
use Symfony\Component\HttpFoundation\Request;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ServiceController extends AbstractController
{
    /**
     * @Route("/admin/service_list", name="service_list")
     */
    public function index(ServiceRepository $rep): Response
    {
        $services = $rep->findAll();
        
        return $this->render('backoffice/services/service_list.html.twig', [
            'services' => $services,
        ]);
    }

    /**
     * @Route("/categories-services/services/{slug}", name="services", methods={"GET"} )
     * 
     */
    public function serviceList(
        EntityManagerInterface $em, 
        $slug, 
        CategoryServiceRepository $rep,
        SeoPageInterface $seoPage,
        ServiceRepository $serviceRepo
        ): Response
    {
        $cache = new FilesystemAdapter();
        
        // $categories = $cache->get("service-page-categorie".$slug, function(ItemInterface $item) use ($rep, $slug){
        //      $item->expiresAfter(DateInterval::createFromDateString('3 hour'));
        //     return $rep->findOneBy(['slug'=>$slug]);
        // });

        $categories = $rep->findOneBy(['slug'=>$slug]);

       
        
        // $services = $em->createQuery("select s From App\Entity\Service s where s.category = :id")
        //     ->setParameter("id", $categories->getId())
        //     ->getResult();
        //dd($categories->getId());
        $services = $serviceRepo->findByCategoryServiceId($categories->getId());



        
        // $services = $cache->get("service-page-service-".$slug, function(ItemInterface $item) use($services){
        //     $item->expiresAfter(DateInterval::createFromDateString('3 hour'));
        //     return $services;
        // });

        $seoPage
                ->setTitle($categories->getSlug())
                ->addMeta('name', 'description', $categories->getDescription())
                ->addMeta('property', 'og:title', $categories->getSlug())
                ->addMeta('property', 'og:type', 'blog')
                ->addMeta('property', 'og:description', $categories->getDescription())
            ;

        return $this->render('frontoffice/services.html.twig', [
            'services' => $services,
            'categories'=>$categories,
        ]);
    }

    /**
     * permet à l'admin d'ajouter un service
     * @param EntityManagerInterface $em
     * @param Request $req
     * @return Response
     * @Route("/admin/service_add", name="service_add")
     */
    public function addService(
        EntityManagerInterface $em, 
        Request $req,
        FlashyNotifier $flashy
        ):Response{
        $service = new Service();
        $form= $this->createForm(ServiceType::class, $service);
        $form->handleRequest($req);

        if($form->isSubmitted() and $form->isValid()){
            $service->setUpdatedAt(new \DateTime('now'));
            $em->persist($service);
            $em->flush();
            $flashy->success("Added successfully ! ",'');
            return $this->redirectToRoute('service_list');
        }


        return $this->render('backoffice/services/service_add.html.twig', [
            'form'=>$form->createView(),
        ]);
    }

    /**
     * Permet à l'admin de supprimer un service
     * @param $id
     * @param EntityManagerInterface $em
     * @return Response
     * @Route("/admin/delete_service/{id}", name="service_delete")
     */
    public function deleteService(
        Service $service, 
        EntityManagerInterface $em,
        FlashyNotifier $flashy
        ):Response{
       
        $em->remove($service);
        $em->flush();
        $flashy->success("Deleted successfully ! ",'');
    return $this->redirectToRoute('service_list');
    }

    /**
     * @param $idService
     * @param Request $req
     * @param ServiceRepository $rep
     * @param EntityManagerInterface $em
     * @return Response
     * @Route("/admin/edit_service/{id}", name="service_edit")
     */
    public function editService(
        Service $service,
        Request $req, 
        EntityManagerInterface $em,
        FlashyNotifier $flashy
        ):Response{
       
        $form= $this->createForm(ServiceType::class, $service);
        $form->handleRequest($req);

        if($form->isSubmitted() and $form->isValid()){
            $service->setUpdatedAt(new \DateTime('now'));
            $em->flush();
            $flashy->success("Edited successfully! ",'');
            return $this->redirectToRoute('service_list');
        }
       return $this->render('backoffice/services/edit_service.html.twig', [
            'form'=>$form->createView(),
       ]);
    }
}
