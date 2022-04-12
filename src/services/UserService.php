<?php
namespace App\services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

Class UserService{
    private $em;
    private $flash;
    private $encoder;
    private $tokenGenarator;
    
    public function __construct(
        EntityManagerInterface $em, 
        FlashyNotifier $flash,
        UserPasswordEncoderInterface $encoder,
        TokenGeneratorInterface $tokenGenarator
        )
    {
        $this->em=$em;
        $this->flash = $flash;
        $this->encoder = $encoder;
        $this->tokenGenarator = $tokenGenarator;
    }

    public function persistUser(User $user, $role):void{
            //md5(uniqid())
            $user->setActivationToken($this->tokenGenarator->generateToken());
            $user->setRgpd(true);
            $user->setRoles($role);
            $user->setCreatedAt(new \DateTime());
            $hash = $this->encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $this->em->persist($user);
            $this->em->flush();
            $this->flash->success("Votre compte à bien été créer!");
    }

    
}