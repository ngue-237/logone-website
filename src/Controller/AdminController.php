<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\ContactRepository;
use App\Repository\DevisRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{   
    /**
     * @Route("/admin-logone", name="admin_dashboard")
     */
    public function index(
        ArticleRepository $articleRepo,
        DevisRepository $devisRepo,
        ContactRepository $contactRepo
    ): Response
    {
        return $this->render('backoffice/index.html.twig',[
            "articles" => $articleRepo->findAll(),
            "articlesPublished" =>$articleRepo->findAllByPub(),
            "devis"=>$devisRepo->findAll(),
            "devisAccepted" => $devisRepo->findAllAccepted(),
            "devisDone" => $devisRepo->findAllJobDone(),
            "contactRequest" =>$contactRepo->findAll()
        ]);
    }
}
