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

class CommentService extends AbstractController
{
    private $manager;
    private $flashy;
    private $userRepo;
    private $commentRepo;
    

    public function __construct(
        EntityManagerInterface $manager, 
        FlashyNotifier $flashy,
        UserRepository $userRepo,
        CommentsRepository $commentRepo
        )
    {
        $this->manager= $manager;
        $this->flashy = $flashy;
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
            ->setIsPublished(false)
            ->setUser($this->userRepo->find($this->getUser()->getId()));

        $this->manager->persist($comment);
        $this->manager->flush();
        
    }

    public function removeComment(Comments $comment){
        $this->manager->remove($comment);
        $this->manager->flush();
        $this->flashy->success('Success delete !','');
    }

    public function allCommentPublished($id):array{
        
        return $this->commentRepo->findByAllComment($id);
    }
}