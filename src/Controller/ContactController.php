<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\services\MaillerService;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * Permet d'envoyer une demande contacte
     * @Route("/contact", name="contact")
     */
    public function index(
        Request $req, 
        EntityManagerInterface $em,
        MaillerService $mailer,
        FlashyNotifier $flashy
        ): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        
        $form->handleRequest($req);

        if($form->isSubmitted() and $form->isValid()){

            $lastname = $form->get('lastName')->getData();
            $firstname = $form->get('firstName')->getData();
            $to = $form->get('email')->getData();
            
            $subject = "Demande contact";
            $template = "email/contact.html.twig";
            $from = "emmanuelbenjamin.nguetoungoum@esprit.tn";
            $message = $form->get('msg')->getData();

            //dd($form->getData());
            $em->persist($contact);
            $em->flush();
            $mailer->send(
                $subject,
                $to,
                $template,
                [
                "message"=> $message, 
                "lastname"=> $lastname, 
                "firstname"=> $firstname
                ],
                $from
            );

            $flashy->success("Votre demande a été bien prise en compte vous serez recontactez dans les prochaines 24h!",'');

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
