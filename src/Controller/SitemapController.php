<?php

namespace App\Controller;

use App\Repository\CategoryServiceRepository;
use App\Repository\ServiceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SitemapController extends AbstractController
{
    /**
     * @Route("/sitemap.xml", name="app_sitemap", defaults={"_format"="xml"})
     */
    public function index(
        Request $req,
        CategoryServiceRepository $categoryServiceRepo,
        ServiceRepository $serviceRepo
        ): Response
    {
        $hostname =  $req->getSchemeAndHttpHost();

        //initialisation d'un tableau pour la liste d'url
        $urls = [];

        //Ajout des urls statisques
        $urls []= ['loc'=> $this->generateUrl("home")];
        $urls []= ['loc'=> $this->generateUrl("about")];
        $urls []= ['loc'=> $this->generateUrl("categorie_service_all")];
        $urls []= ['loc'=> $this->generateUrl("app_rdv")];
        $urls []= ['loc'=> $this->generateUrl("blog")];
        $urls []= ['loc'=> $this->generateUrl("jobslist_front")];
        
        //ajout des urls dynamique
         foreach($categoryServiceRepo->findAll() as $catgService){
            $images = [
                "loc" => "/public/uploads/images/categories_service/".$catgService->getImage(),
                "title"=>$catgService->getSlug()
            ];
            $urls []= [
                "loc"=>$this->generateUrl("categorie_service_all"),
                "images" => $images,
                // "lastmod" =>$service->getUpdatedAt()->format('Y-m-d')
            ];
            // dd($catgService);
            
            
        }
        
         foreach($categoryServiceRepo->findAll() as $catgService){
            // $images = [
            //     "loc" => "/public/uploads/images/categories_service/".$catgService->getImage(),
            //     "title"=>$catgService->getSlug()
            // ];
            foreach($catgService->getServices() as $service){
                $urls []= [
                "loc"=>$this->generateUrl("services", [
                    "slug"=>$service->getSlug()
                ]),
                 //"images" => $images,
                "lastmod" =>$service->getUpdatedAt()->format('Y-m-d')
            ];
            }
            
        }

        //dd($urls);

        $response = new Response(
            $this->renderView('sitemap/index.html.twig', compact(
            "hostname",
            "urls"
            )),200
        );

        $response->headers->set("content-type", "text/xml");
        return $response;
    }
}
