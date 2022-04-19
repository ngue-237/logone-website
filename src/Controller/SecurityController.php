<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\ResetPassType;
use App\services\CurlService;
use App\services\UserService;
use App\services\MaillerService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;

class SecurityController extends AbstractController
{
    private FormLoginAuthenticator $authenticator;
     public function __construct(
       FormLoginAuthenticator $authenticator 
    ) 
    {
        $this->authenticator = $authenticator;
    }

    /**
     * permet à un visiteur de s'inscrire via un formulaire d'inscription
     * @Route("/s-inscrire", name="security_register")
     */
    public function register(
        UserService $helper,
        Request $req,
        MaillerService $mailerHelper,
        CurlService $client,
        FlashyNotifier $flashy,
        UserAuthenticatorInterface $authenticatorManager
        ): Response
        
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->add('password', PasswordType::class)
        ->add('passwordConfirm', PasswordType::class)
        ->add("rgpd", CheckboxType::class, [
                "constraints"=>[
                    new NotBlank(),
                    new NotNull()
                ]
            ])
            ;
        $form->add("captcha", HiddenType::class, [
            "mapped"=>false,
            "constraints"=>[
                new NotNull(),
                new NotBlank()
            ]
        ]);
        $form->handleRequest($req);
        
        if($form->isSubmitted() and $form->isValid()){
                //dd("hello");
                $url = "https://www.google.com/recaptcha/api/siteverify?secret=6Lc96AYfAAAAAEP84ADjdx5CBfEpgbTyYqgemO5n&response={$_POST['user']["captcha"]}";
                
                $response = $client->curlManager($url);
                if(empty($response) || is_null($response)){
                
                $this->addFlash("warning",'something wrong!');
                return $this->redirectToRoute('security_register');
                }else{
                    $data = json_decode($response);
                    if($data->success){
                   
                    $helper->persistUser($user, ["ROLE_USER"]);
                        $mailerHelper->send(
                     "Activation de votre compe", 
                     $form->get("email")->getData(), 
                     "email/activation.html.twig", 
                     ["activationToken" => $user->getActivationToken() ],
                     "no-reply@logonedigital.com"
                    );
                    $authenticatorManager->authenticateUser($user, $this->authenticator, $req, [new RememberMeBadge()]);
                    $flashy->success("Sucess Registration", "");
                    
                    return $this->redirectToRoute('home');  
                    }else{
                        $flashy->error("Confirm you are not robot!", "");
                         return $this->redirectToRoute('security_register');
                    }
                }
                
        }
        return $this->render('backoffice/registration.html.twig', [
        'form'=>$form->createView(), 
        "errors"=>$form->getErrors()
        ]);
    }

    /**
     * activation de du compte grâce au token
     *
     * @return void
     * @Route("/register/activation/{activationToken}", name="activation")
     */
    public function activation(
        User $user, 
        EntityManagerInterface $em,
        FlashyNotifier $flash
        ){

        if(!$user){
            throw $this->createNotFoundException("cette utilisateur n'existe pas");
        }
        $user->setActivationToken(null);
        $em->persist($user);
        $em->flush();

        //message flash
        $flash->success("Votre Compte à bien été activé", "");
        return $this->redirectToRoute('home');
        
    }

    /**
     * permet à un administrateur d'ajouter un autre administrateur
     * @param Request $req
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     * @Route("/admin/add_admin", name="add_admin")
     */
    public function adminAdd(UserService $helper, Request $req, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder):Response{
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->add('password', PasswordType::class, [
            "constraints"=>[
                new Regex([
                        "pattern"=>"/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}+ $/",
                        "match"=>false,
                        "message"=>"your phonenumber is not correct"
                    ])
            ]
        ])
            ->add('passwordConfirm', PasswordType::class, [
                "constraints"=>[
                    new NotBlank(),
                    new NotNull()
                ]
            ])
            ->add("rgpd", CheckboxType::class, [
                "constraints"=>[
                    new NotBlank()
                ]
            ]);
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid()){ 
            $helper->persistUser($user, ["ROLE_ADMIN"]);
            return $this->redirectToRoute('user_list');
        }
        return $this->renderForm('backoffice/security/admin_register.html.twig', compact('form'));
    }

    /**
     * @param UserRepository $rep
     * @return Response
     * @Route("/admin/user_list", name="user_list")
     */
    public function userList(UserRepository $rep):Response{
        return $this->render('backoffice/security/user_list.html.twig',[
            'users'=>$rep->findAll()
        ]);
    }

    /**
     * permet de supprimer un utilisateur selon son ID
     * @param $id
     * @param EntityManagerInterface $em
     * @return Response
     * @Route("/admin/user-delete/{id}", name="user_delete")
     */
    public function userDelete(User $user, EntityManagerInterface $em):Response{
        $em->remove($user) ;
        $em->flush();
    return $this->redirectToRoute('user_list');
    }

    /**
     * Permet à l'administrateur de modifier les propriétés d'un utilisateur
     * @param $id
     * @param EntityManagerInterface $em
     * @param Request $req
     * @return Response
     * @Route("/admin/user-edit/{id}", name="user_edit")
     */
    public function userEdit(User $user, EntityManagerInterface $em, Request $req):Response{
        
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($req);

        if($form->isSubmitted() and $form->isValid())
        {

            $em->flush();
            return $this->redirectToRoute('user_list');
        }
        return $this->renderForm('backoffice/security/admin_user_edit.html.twig',compact('form'));
    }

    /**
     * Permet à un utilisateur de s'authentifier
     * @param AuthenticationUtils $auth
     * @param Request $req
     * @return Response
     * @Route("/se-connecter", name="security_login")
     */
    
    public function login(AuthenticationUtils $auth, Request $req):Response{
        $errors = $auth->getLastAuthenticationError();
        $lastUsername = $auth->getLastUsername();
        
        return $this->render('backoffice/login.html.twig', [
            'last_username'=>$lastUsername,
            'errors'=>$errors,
            'redirect'=>$req->headers->get("referer")
        ]);
    }

    

    /**
     * Permet à un utilisateur de se déconnecter
     * @Route("/se-deconnecter", name="security_logout")
     */
    public function logout(){
    }

    /**
     * permet de récupérer un mot de pass
     *
     * @Route("/oubli-pass", name="app_forgotten_password")
     */
    public function forgottenPassword(
        Request $req,
         MaillerService $mailerHelper,
        UserRepository $userRepo,
        FlashyNotifier $flashy,
        TokenGeneratorInterface $tokenGenarator,
        EntityManagerInterface $em

    ){
        $form = $this->createForm(ResetPassType::class);
        $form->handleRequest($req);
        if($form->isSubmitted() and $form->isValid()){
            $data = $form->getData();
           // dd($data);
            $user = $userRepo->findOneByEmail($data['email']);
            
            if(!$user){
                $flashy->warning("Attention cette addresse n'existe pas", '');
                return $this->redirectToRoute("security_login");
            }
            $token = $tokenGenarator->generateToken();
            try {
                $user->setResetToken($token);
                $em->persist($user);
                $em->flush();
                
            } catch (\Exception $e) {
                $flashy->warning("Une erreur survenu : ".$e->getMessage(), "");
                return $this->redirectToRoute("security_login");
            }
            
            // dd($user);
            //génération de l'url de réinitialisation
            $url = $this->generateUrl("app_reset_password", [
                "token"=>$token
            ], UrlGeneratorInterface::ABSOLUTE_URL);
            $mailerHelper->send(
                     "Activation de votre compe", 
                     $user->getEmail(), 
                     "email/reset_password.html.twig", 
                     ["token" => $token ],
                     "no-reply@logonedigital.com"
                    );
            //$this->addFlash("success", "un email de réinitialisation du mot de passe vous a été envoyé!");
            $flashy->success("un email de réinitialisation du mot de passe vous a été envoyé !", "");

            return $this->redirectToRoute("security_login");
        }

        return $this->renderForm("backoffice/forgot-password.html.twig", compact('form'));
    }

    /**
     * permet de réinitialiser le mot de passe
     *
     * @Route("/reset-pass/{token}", name="app_reset_password")
     */
    public function resetPassword(
    Request $req, 
    UserPasswordEncoderInterface $encoder, 
    FlashyNotifier $flashy,
    EntityManagerInterface $em,
    UserRepository $userRepo,
    $token
    ){
        $user = $userRepo->findOneBy(["resetToken"=>$token]);
        if(!$user){
            $flashy->warning("token inconnu!");
            return $this->redirectToRoute('security_login');
        }

        //si le formulaire est envoyé en méthode post

        if($req->isMethod("POST")){
            $user->setResetToken(null);

            //on chiffre le mot de passe
            $user->setPassword($encoder->encodePassword($user, $req->request->get('password')));
            $em->persist($user);
            $em->flush();
            $flashy->success("Mot de passe modifier avec succès");
            return $this->redirectToRoute('security_login');
        }
        else{
            return $this->render("backoffice/reset-password.html.twig", compact("token"));
        }
    }

}
