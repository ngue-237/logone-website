<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * Permet d'envoyer une demande contacte
     * @Route("/contact", name="contact")
     */
    public function index(Request $req, EntityManagerInterface $em): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($req);
        if($form->isSubmitted() and $form->isValid()){
            $em->persist($contact);
            $em->flush();
            return $this->redirectToRoute('home');
        }
        return $this->render('frontoffice/contact.html.twig', [
            'formContact'=>$form->createView(),
        ]);
    }

}
