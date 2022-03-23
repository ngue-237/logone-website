<?php
namespace App\services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

Class UserService{
    private $em;
    private $flash;
    private $encoder;
    
    public function __construct(
        EntityManagerInterface $em, 
        FlashyNotifier $flash,
        UserPasswordEncoderInterface $encoder
        )
    {
        $this->em=$em;
        $this->flash = $flash;
        $this->encoder = $encoder;
    }

    public function persistUser(User $user, $role):void{
            $user->setRoles($role);
            $user->setCreatedAt(new \DateTime());
            $hash = $this->encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $this->em->persist($user);
            $this->em->flush();
            $this->flash->success("Votre compte à bien été créer!");
    }

    
}