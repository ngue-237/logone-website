<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comments;
use App\services\CommentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("/comment", name="app_comment")
     */
    public function index(): Response
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }

    /**
     * Permet de supprimer un comment via par son identifiant
     *
     * @param Comments $comment
     * @param CommentService $commentService
     * @param Article $article
     * @return Response
     * @Route("/blog/article/comment_delete/{slug}/", name="comment_author_delete", methods={"DELETE"})
     */
    public function commentDeleteByAuthor(
        Comments $comment,
        CommentService $commentService,
        Article $article,
        $id
        ):Response{
            dd("hello");
            $commentService->removeComment($comment);
            return $this->redirectToRoute('article_detail', ['slug'=> $article->getSlug()]);
    }

    // public function commentAuthorEdit(){

    // }
}
