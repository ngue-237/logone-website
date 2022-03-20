<?php
namespace App\services;

use App\Entity\Article;
use DateTime;
use App\Entity\Comments;
use App\Repository\CommentsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class CommentService extends AbstractController
{
    private $manager;
    private $flash;
    private $userRepo;
    private $commentRepo;
    

    public function __construct(
        EntityManagerInterface $manager, 
        FlashyNotifier $flash,
        UserRepository $userRepo,
        CommentsRepository $commentRepo
        )
    {
        $this->manager= $manager;
        $this->flash = $flash;
        $this->userRepo = $userRepo;
        $this->commentRepo = $commentRepo;
    }

    public function persistComment(
        Comments $comment, 
        Article $article
        ):void
    {
        $comment->setCreatedAt(new DateTime('now'))
            ->setIsPublished(false)
            ->setArticle($article)
            ->setUser($this->userRepo->find($this->getUser()->getId()));

        $this->manager->persist($comment);
        $this->manager->flush();
        $this->flash->success('Votre commentaire a bien été envoyé, merci. Il sera publié après validation','');
    }

    public function removeComment(Comments $comment){
        $this->manager->remove($comment);
        $this->manager->flush();
        $this->flash->success('Success delete !','');
    }

    public function allCommentPublished():array{
        
        return $this->commentRepo->findByAllComment();
    }
}