<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\services\UserService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * permet à un visiteur de s'inscrire via un formulaire d'inscription
     * @Route("/register", name="security_register")
     */
    public function register(
        UserService $helper,
        Request $req): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->add('password', PasswordType::class)
        ->add('passwordConfirm', PasswordType::class)
        ->add("rgpd", CheckboxType::class, [
                "constraints"=>[
                    new NotBlank()
                ]
            ]);
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid()){
                $helper->persistUser($user, ["ROLE_USER"]);
                
            return $this->redirectToRoute('security_login');
        }
        return $this->render('frontoffice/register.html.twig', [
        'form'=>$form->createView()
        ]);
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
                    new NotBlank()
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
        $users = $rep->findAll();
        return $this->render('backoffice/security/user_list.html.twig',[
            'users'=>$users
        ]);
    }

    /**
     * permet de supprimer un utilisateur selon son ID
     * @param $idUser
     * @param UserRepository $rep
     * @param EntityManagerInterface $em
     * @return Response
     * @Route("/admin/user_delete/{id}", name="user_delete")
     */
    public function userDelete(User $user, UserRepository $rep, EntityManagerInterface $em):Response{
        $em->remove($user) ;
        $em->flush();
    return $this->redirectToRoute('user_list');
    }

    /**
     * Permet à l'administrateur de modifier les propriétés d'un utilisateur
     * @param $idUser
     * @param UserRepository $rep
     * @param EntityManagerInterface $em
     * @param Request $req
     * @return Response
     * @Route("/admin/user_edit/{id}", name="user_edit")
     */
    public function userEdit(User $user, UserRepository $rep, EntityManagerInterface $em, Request $req):Response{
        
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
     * @Route("/login", name="security_login")
     */
    
    public function login(AuthenticationUtils $auth, Request $req):Response{
        return $this->render('frontoffice/login.html.twig');
    }

    /**
     * Permet à un utilisateur de se déconnecter
     * @Route("/logout", name="security_logout")
     */
    public function logout(){

    }

}
