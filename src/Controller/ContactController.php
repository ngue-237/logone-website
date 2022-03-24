<?php

namespace App\Controller;

use Curl\Curl;
use App\Entity\Contact;
use App\Form\ContactType;
use App\services\CaptchaService;
use App\services\MaillerService;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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
        FlashyNotifier $flashy,
        CaptchaService $helper
        ): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->add("captcha", HiddenType::class, [
            "constraints"=>[
                new NotNull(),
                new NotBlank()
            ]
        ]);
     
        
        $form->handleRequest($req);

        if($form->isSubmitted() and $form->isValid()){
            $url = "https://www.google.com/recaptcha/api/siteverify?secret=6Lc96AYfAAAAAEP84ADjdx5CBfEpgbTyYqgemO5n&response={$_POST['contact']["captcha"]}";
            $curl = curl_init($url);

            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($curl);
            
            if(empty($response) || is_null($response)){
                
                $flashy->warning("Something wrong!",'');
                return $this->redirectToRoute('contact');
            }
            else{
                //dd($form->getData());
                $data = json_decode($response);
                // $lastname = $form->get('lastName')->getData();
                // $firstname = $form->get('firstName')->getData();
                // $to = $form->get('email')->getData();
                
                // $subject = "Demande contact";
                // $template = "email/contact.html.twig";
                // $from = "emmanuelbenjamin.nguetoungoum@esprit.tn";
                // $message = $form->get('msg')->getData();

                
                if($data->success){
                   // dd($data);
                    $em->persist($contact);
                    $em->flush();
                    $mailer->send(
                        "Demande contact",
                        $form->get('email')->getData(),
                        "email/contact.html.twig",
                        [
                        "message"=> $message = $form->get('msg')->getData(), 
                        "lastname"=> $form->get('lastName')->getData(), 
                        "firstname"=> $form->get('firstName')->getData()
                        ],
                        "emmanuelbenjamin.nguetoungoum@esprit.tn"
                    );

                    $flashy->success("Votre demande a été bien prise en compte vous serez recontactez dans les prochaines 24h!",'');
                    return $this->redirectToRoute('contact');
                }else{
                    //dd("je suis là");
                    $flashy->error("Confirm you are not robot!",'');
                    return $this->redirectToRoute('contact');
                }

            }
           

            
                
            
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
