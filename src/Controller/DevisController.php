<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DevisController extends AbstractController
{
    /**
     * @Route("/devis", name="app_devis")
     */
    public function index(): Response
    {
        return $this->render('devis/index.html.twig', [
            'controller_name' => 'DevisController',
        ]);
    }
}
