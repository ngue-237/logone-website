<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Entity\Service;
use App\services\CurlService;
use App\services\DevisService;
use App\Entity\CategoryService;
use App\Form\ServiceType;
use App\services\MaillerService;
use App\Repository\DevisRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;



class DevisController extends AbstractController
{
    

    /**
     * permet générer un formulaire avec un sélect dynamique et ajouter un dévis
     *
     * @param Request $req
     * @param EntityManagerInterface $em
     * @return Response
     * @Route("/categories-services/services/{slug}/devis", name="devis_add", methods={"GET" ,"POST"})
     */
    public function addDevis(
         CategoryService $categoryService,
         Request $req, 
         ServiceRepository $serviceRepo,
         DevisService $devisHelper,
         FlashyNotifier $flashy,
         MaillerService $mailer,
         CurlService $client,
         $slug
        ):Response{
        $devis = new Devis();
        $services = $serviceRepo->findByCategoryService($categoryService->getId());
        
        // $servicess[] = new Service();
        // dd($servicess instanceof Service);
        // for($i=0; $i<count($services); $i++){
        //     $servicess[$i]->setDesignation($services[$i]->getDesignation()) ;
        // }
       
        

        $form = $this->createFormBuilder(['categories'=>$categoryService, "services"=>$services])
            -> add('lastname', TextType::class,
            [   
            'constraints' => [new Length([
                'min' => 4,
                "minMessage"=>"Your lastname must be at least {{ limit }} characters long",
                "max" => 50,
                "maxMessage"=>"Your first name cannot be longer than {{ limit }} characters",
                ])],
            ])
            ->add('firstname', TextType::class,
            [
                
            'constraints' => [new Length([
                'min' => 4,
                "minMessage"=>"Your lastname must be at least {{ limit }} characters long",
                "max" => 50,
                "maxMessage"=>"Your first name cannot be longer than {{ limit }} characters",
                ])],
            ]
            )
            ->add('email', EmailType::class, [
                "constraints"=>[
                    new NotBlank(),
                    new Email([
                        "message" => "The email '{{ value }}' is not a valid email."
                    ])
                ]
            ])
            ->add('phoneNumber', TelType::class, [
               
                "constraints"=>[
                    new NotBlank(),
                    new Regex([
                        "pattern"=>"/^(\(0\))?[0-9]+$/",
                        "match"=>false,
                        "message"=>"your phonenumber is not correct"
                    ])
                ]
            ])
            ->add('company', TextType::class, [
                "constraints"=>[
                    new NotBlank()
                ]
            ])
            ->add('country',TextType::class, [
                "constraints"=>[
                    new NotBlank()
                ]
            ] )
            
            ->add('subject', TextareaType::class, [
                "constraints"=>[
                    new NotBlank(),
                    new Length([
                    'min' => 4,
                    "minMessage"=>"Your message must be at least {{ limit }} characters long",
                    "max" => 600,
                    "maxMessage"=>"Your message  cannot be longer than {{ limit }} characters",
                    ])
                ]
            ])
            ->add("rgpd", CheckboxType::class, [
                "constraints"=>[
                    new NotBlank()
                ]
            ])
            ->add('categories', EntityType::class,[
                'class'=>CategoryService::class,
                'choice_label'=>'designation',
                'label'=>false,
                "constraints"=>[
                    new NotBlank(),
                ]
            ])
            ->add('services', ChoiceType::class,[
                "choices"=>$services,
                'choice_label' => 'designation',
                "constraints"=>[
                    new NotBlank(),
                ]
            ])
            ->add("captcha", HiddenType::class, [
            "constraints"=>[
                new NotNull(),
                new NotBlank()
            ]
        ])
            ->getForm();

        $devisRoute = $this->generateUrl("devis_add",["slug"=>$slug],UrlGeneratorInterface::ABSOLUTE_URL);
   
        $form->handleRequest($req);
        if($form->isSubmitted() and $form->isValid()){
            $url = "https://www.google.com/recaptcha/api/siteverify?secret=6Lc96AYfAAAAAEP84ADjdx5CBfEpgbTyYqgemO5n&response={$form->get("captcha")->getData()}";
            $response = $client->curlManager($url);
      
            if(empty($response) || is_null($response)){
                $flashy->error("Something wrong!",'');
                return $this->redirect($devisRoute);

            }else{
                $data = json_decode($response);
                if($data->success){    
                    $devisSet = new Devis();
                    $devisSet = $devisHelper->setDevis($devis, $form);
                    
                            
                    $flashy->success("Un email de confirmation vous a-été envoyé à l'adresse ".$form->get("email")->getData(),'');

                    return $this->redirect($devisRoute);
                }else{
                    $flashy->error("Un problème est survenu lors de l'envoi du formulaire, svp veuillez remplir le formulaire une fois de plus",'');
                    return $this->redirect($devisRoute);
                }
            }
            
        }
        return $this->renderForm('frontoffice/devis_add.html.twig',compact(
            'form', 
            'categoryService'
        ));
    }

    /**
     * activation de du compte grâce au token
     *
     * @return void
     * @Route("/services/devis/confirmation/{confirm}", name="confirmation-devis")
     */
    public function DevisConfirmation(
        Devis $devis, 
        EntityManagerInterface $em,
        FlashyNotifier $flash
        ){

        if(!$devis){
            throw $this->createNotFoundException("Ce devis n'existe pas");
        }
        $devis->setConfirm(null);
        $em->persist($devis);
        $em->flush();

        $flash->success("Votre demande à bien été confirmé !", "");
        return $this->redirectToRoute('home');
        
    }

    /**
     * set devis closing status
     *@Route("/admin/devis/is-sale/{id}" ,name="admin_closing_status")
     * @return void
     */
    public function devisAccepted(
        EntityManagerInterface $em,
        Devis $devis,
        Request $req
    ){
        $submittedToken = $req->request->get('token');
      
        if ($this->isCsrfTokenValid('devis-closed', $submittedToken)) {
            
             $devis->setClosingStatus(true);
             $em->flush();
            //  dd("hello boy");
            return $this->redirectToRoute('devis_lists');
        }
        return $this->redirectToRoute('devis_lists');
    }
    /**
     * set devis closing status
     *@Route("/admin/devis/job-is-ok/{id}" ,name="admin_job_done")
     * @return void
     */
    public function jobDone(
        EntityManagerInterface $em,
        Devis $devis,
        Request $req
    ){
        $submittedToken = $req->request->get('token');
      
        if ($this->isCsrfTokenValid('job-done', $submittedToken)) {
            
             $devis->setJobDone(true);
             $em->flush();

            return $this->redirectToRoute('devis_lists');
        }
        return $this->redirectToRoute('devis_lists');
    }

    /**
     * liste tout les devis qui sont dans la bd dans l'ordre décroissant
     *
     * @param DevisRepository $devisRepo
     * @return Response
     * @Route("/admin/devis_lists", name="devis_lists")
     */
    public function getAllDevis(DevisRepository $devisRepo):Response{
       
        return $this->render('backoffice/devis/devis_list.html.twig',[
            'devis'=>$devisRepo->findAllOderDesc()
        ]);
    }

    /**
     * permet de supprimer un devis
     *
     * @param DevisRepository $devisRepo
     * @param EntityManagerInterface $em
     * @return Response
     * @Route("/admin/devis_delete/{id}", name="devis_delete", methods={"GET" ,"DELETE"})
     */
    public function deleteDevis(
        Devis $devis, 
        EntityManagerInterface $em,
        FlashyNotifier $flashy
    ):Response{
        
        $em->remove($devis);
        $em->flush();
        $flashy->success("Delete successfully","");
        return $this->redirectToRoute('devis_lists');
    }
}
