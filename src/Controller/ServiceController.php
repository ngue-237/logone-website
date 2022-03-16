<?php

namespace App\Controller;

use App\Entity\CategoryService;
use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\CategoryServiceRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends AbstractController
{
    /**
     * @Route("/admin/service_list", name="service_list")
     */
    public function index(ServiceRepository $rep): Response
    {
        $services = $rep->findAll();
        //dd($services);
        return $this->render('backoffice/services/service_list.html.twig', [
            'services' => $services,
        ]);
    }

    /**
     * @Route("/services/{slug}", name="services", methods={"GET"} )
     * 
     */
    public function serviceList(EntityManagerInterface $em, $slug, CategoryServiceRepository $rep): Response
    {
        $categories = $rep->findOneBy(['slug'=>$slug]);
        
        if(!$categories){
            throw $this->createNotFoundException("cette cathégorie n'existe pas");
        }
        
        $query = $em->createQuery("select s From App\Entity\Service s where s.category = :id")
            ->setParameter("id", $categories->getId());
        $services = $query->getResult();
       
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
    public function addService(EntityManagerInterface $em, Request $req):Response{
        $service = new Service();
        $form= $this->createForm(ServiceType::class, $service);
        $form->handleRequest($req);

        if($form->isSubmitted() and $form->isValid()){
            $em->persist($service);
            $em->flush();
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
     * @Route("/admin/delete_service/{idService}", name="service_delete")
     */
    public function deleteService($idService, EntityManagerInterface $em, ServiceRepository $rep):Response{
        $service = $rep->find($idService);
        $em->remove($service);
        $em->flush();

    return $this->redirectToRoute('service_list');
    }

    /**
     * @param $idService
     * @param Request $req
     * @param ServiceRepository $rep
     * @param EntityManagerInterface $em
     * @return Response
     * @Route("/admin/edit_service/{idService}", name="service_edit")
     */
    public function editService($idService, Request $req, ServiceRepository $rep, EntityManagerInterface $em):Response{
        $service = $rep->find($idService);
        $form= $this->createForm(ServiceType::class, $service);
        $form->handleRequest($req);

        if($form->isSubmitted() and $form->isValid()){
            $em->flush();
            return $this->redirectToRoute('service_list');
        }
       return $this->render('backoffice/services/edit_service.html.twig', [
            'form'=>$form->createView(),
       ]);
    }
}
