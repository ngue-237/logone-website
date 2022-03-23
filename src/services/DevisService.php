<?php
namespace App\services;

use App\Entity\Devis;
use Doctrine\ORM\EntityManagerInterface;

class DevisService{
    private $devis;
    private $em;
    private $mailer;

    public function __construct(
        EntityManagerInterface $em, 
        MaillerService $mailer
        )
    {
        $this->em=$em;
        $this->mailer = $mailer;
    }

   
    public function setDevis(Devis $devis, $form){
            $devis->setFirstname($form->get("firstname")->getData());
            $devis->setLastname($form->get("lastname")->getData());
            $devis->setEmail($form->get("email")->getData());
            $devis->setPhoneNumber($form->get("phoneNumber")->getData());
            $devis->setCompany($form->get("company")->getData());
            $devis->setCountry($form->get("country")->getData());
            $devis->setSubject($form->get("subject")->getData());
            $devis->setCategories($form->get("categories")->getData());
            $devis->setCreatedAt(new \DateTime());
            $this->em->persist($devis);
            $this->em->flush();
            

            $this->mailer->send(
                "Demande de devis",
                $form->get("email")->getData(),
                "email/contact.html.twig",
                [
                "message"=> $form->get("subject")->getData(), 
                "lastname"=> $form->get("lastname")->getData(), 
                "firstname"=> $form->get("firstname")->getData()
                ],
                "emmanuelbenjamin.nguetoungoum@esprit.tn"
            );
            return $devis;
    }
}