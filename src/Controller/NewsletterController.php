<?php

namespace App\Controller;

use App\Entity\Newsletter;
use App\Repository\NewsletterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewsletterController extends AbstractController
{
    
    /**
     * Undocumented function
     *
     * @param Request $request
     * @return Response
     * @Route("/newsletter", name="post_newsletter",methods={"GET","POST"})
     */
    public function newsletter(Request $request,EntityManagerInterface $manager, NewsletterRepository $newsletterRepository):Response{
        //dd( $request);
        $data = $request->getContent();
        $data = json_decode($data, true);
        $email = new Newsletter();
        $email->setEmail($data['email']);
        $manager->persist($email);
        $manager->flush();

            return $this->json([
                'code'=>200,
                'message'=>'Mail ajouté au newsletters'
            ],200);
        
    }
}
