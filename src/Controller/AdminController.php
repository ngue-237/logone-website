<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin_dashboard", name="admin_dashboard")
     */
    public function index(): Response
    {
        return $this->render('backoffice/index.html.twig', [
        ]);
    }
}
