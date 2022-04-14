<?php
namespace App\services;

use App\Entity\Devis;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class DevisService{
    private $devis;
    private $em;
    private $mailer;
    private $tokenGenarator;

    public function __construct(
        EntityManagerInterface $em, 
        MaillerService $mailer,
        TokenGeneratorInterface $tokenGenarator
        )
    {
        $this->em=$em;
        $this->mailer = $mailer;
        $this->tokenGenarator = $tokenGenarator;
    }

   
    public function setDevis(Devis $devis, $form){
            
            $devis->setFirstname($form->get("firstname")->getData());
            $devis->setLastname($form->get("lastname")->getData());
            $devis->setEmail($form->get("email")->getData());
            $devis->setPhoneNumber($form->get("phoneNumber")->getData());
            $devis->setCompany($form->get("company")->getData());
            $devis->setCountry($form->get("country")->getData());
            $devis->setSubject($form->get("subject")->getData());
            $devis->setConfirm($this->tokenGenarator->generateToken());
            $devis->setCategories($form->get("categories")->getData());
            $devis->setServices($form->get("services")->getData());
            $devis->setCategories($form->get("categories")->getData());
            $devis->setRgpd(true);
            $devis->setCreatedAt(new \DateTime());
            $this->em->persist($devis);
            $this->em->flush();
            

            $this->mailer->send(
                "Demande de devis",
                $form->get("email")->getData(),
                "email/devis.html.twig",
                [
                "message"=> $form->get("subject")->getData(), 
                "lastname"=> $form->get("lastname")->getData(), 
                "firstname"=> $form->get("firstname")->getData(),
                "confirm" => $devis->getConfirm()
                ],
                "emmanuelbenjamin.nguetoungoum@esprit.tn"
            );
            return $devis;
    }
}