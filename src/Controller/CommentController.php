<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comments;
use App\Repository\ArticleRepository;
use App\services\CommentService;
use App\Repository\CommentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Cache\CacheInterface;

class CommentController extends AbstractController
{
    /**
     * @Route("/admin/comments_list", name="admin_comments_list")
     */
    public function index(CommentsRepository $commentRepo): Response
    {
    
        return $this->render('backoffice/comments/list_comment.html.twig', [
            'comments' => $commentRepo->findAllByDate(),
        ]);
    }

    /**
     * permet à l'admin de publier un commentaire
     *
     * @param EntityManagerInterface $em
     * @param CommentsRepository $commentRepo
     * @return void
     * @Route("/admin/comment_publish/{id}", name="admin_comment_publish")
     */
    public function publish(
        EntityManagerInterface $em,
        Comments $comment,
        Request $req,
        CacheInterface $cache
    ){
        $submittedToken = $req->request->get('token');
      
        if ($this->isCsrfTokenValid('publish-comment', $submittedToken)) {
            
             $comment->setIsPublished(true);
             $em->flush();
            //  dd("hello boy");
            $cache->delete("article-comments-article-detail-page");
            return $this->redirectToRoute('admin_comments_list');
        }
        return $this->redirectToRoute('admin_comments_list');
    }
    /**
     * Permet à l'administrateur de supprimer un commentaire
     * @param Comments $comment
     * @param CommentService $commentService
     * @return Response
     * @Route("/admin/comment_delete/{id<\d+>?1}", name="comment_admin_delete", methods={"DELETE","GET"})
     */
    public function adminCommentDelete(
        Comments $comment,
        CommentService $commentService
        ):Response{
            $commentService->removeComment($comment);
            return $this->redirectToRoute('admin_comments_list');
    }

    // public function addComment(){

    // }

    /**
     * Permet de supprimer un comment via par son identifiant
     *
     * @param Comments $comment
     * @param CommentService $commentService
     * @param Article $article
     * @return Response
     * @Route("/blog/article/comment_delete/{id}/{slug}", name="comment_author_delete", methods={"DELETE", "GET", "POST"})
     */
    public function commentDeleteByAuthor(
        CommentsRepository $commentRepo,
        CommentService $commentService,
        ArticleRepository $articleRepo,
        $slug,
        $id
        ):Response{
            $this->denyAccessUnlessGranted('POST_DELETE',$commentRepo->find($id));

            $commentService->removeComment($commentRepo->find($id));
            return $this->redirectToRoute('article_detail', ['slug'=> $articleRepo->findOneBy(['slug'=>$slug])->getSlug()]);
    }

    /**
     * Undocumented function
     *
     * @param CommentsRepository $commentRepo
     * @param CommentService $commentService
     * @param ArticleRepository $articleRepo
     * @param [type] $slug
     * @param [type] $id
     * @return void
     * @Route("/blog/article/comment_delete/{id}", name="comment_author_delete_ajax")
     */
    public function deleteAuthorComment(
        CommentService $commentService,
        Request $req,
        Comments $comment,
        EntityManagerInterface $em
    ):JsonResponse{
    
        dd($this->denyAccessUnlessGranted('POST_DELETE',$comment));
        $data = json_decode($req->getContent(),true);
     
        if($this->isCsrfTokenValid('comment-delete', $data['_token'])){
      
          $commentService->removeComment($comment);
          return new JsonResponse([
                        'commentaires'=>$commentService->allCommentPublished(),
                        'message'=>'Votre commentaire a bien été supprimer', 200]);
        }    
        else{

            return new JsonResponse([
                "errors"=>"Le commentaire n'a pas été supprimer!"
            ,400]);
        }
            
    }

    
    
}
