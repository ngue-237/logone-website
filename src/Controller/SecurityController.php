<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
class SecurityController extends AbstractController
{
    /**
     * permet à un visiteur de s'inscrire via un formulaire d'inscription
     * @Route("/register", name="security_register")
     */
    public function register(Request $req, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->add('password', PasswordType::class)
        ->add('passwordConfirm', PasswordType::class);
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid()){
            $user->setRoles(['ROLE_USER']);
            $user->setCreatedAt(new \DateTime());
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $em->persist($user);
            $em->flush();
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
    public function adminAdd(Request $req, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder):Response{
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->add('password', PasswordType::class)
            ->add('passwordConfirm', PasswordType::class);
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid()){
            $user->setRoles(['ROLE_ADMIN']);
            $user->setCreatedAt(new \DateTime());
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('user_list');
        }
        return $this->render('backoffice/security/admin_register.html.twig', [
            'form'=>$form->createView()
        ]);
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
     * @Route("/admin/user_delete/{idUser}", name="user_delete")
     */
    public function userDelete($idUser, UserRepository $rep, EntityManagerInterface $em):Response{
        $em->remove($rep->find($idUser)) ;
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
     * @Route("/admin/user_edit/{idUser}", name="user_edit")
     */
    public function userEdit($idUser, UserRepository $rep, EntityManagerInterface $em, Request $req):Response{
        $user = $rep->find($idUser);
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($req);

        if($form->isSubmitted() and $form->isValid())
        {

            $em->flush();
            return $this->redirectToRoute('user_list');
        }
        return $this->render('backoffice/security/admin_user_edit.html.twig',[
        'form'=>$form->createView()
        ]);
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
