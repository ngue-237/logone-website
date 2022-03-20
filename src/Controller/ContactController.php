<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
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
            return $this->redirectToRoute('contact');
        }
        return $this->renderForm('frontoffice/contact.html.twig', compact('form'));
    }

    /**
     * Permet d'afficher la liste des contact
     * @param ContactRepository $rep
     * @Route("/admin/contact_list", name="contact_list")
     */
    public function contactList(ContactRepository $rep):Response{
       
        return $this->render('backoffice/contact/contactList.html.twig', [
            'contacts'=>$rep->findAll()
        ]);
    }

    /**
     * Permet de supprimer un contact
     * @param $id
     * @param ContactRepository $rep
     * @param EntityManagerInterface $em
     * @return Response
     * @Route("/admin/contact_delete/{id}", name="contact_delete")
     */
    public function deleteContact(Contact $conctact , ContactRepository $rep, EntityManagerInterface $em):Response{
        $em->remove($conctact);
        $em->flush();
        return $this->redirectToRoute('contact_list');
    }

}
