<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Article;
use App\Entity\Comments;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\services\CommentService;
use App\Repository\LikeRepository;
use App\Repository\ArticleRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CategoryArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    /**
     * @Route("/admin/article", name="article_list_admin")
     */
    public function index(ArticleRepository $articleRepo): Response
    {
        return $this->render('backoffice/article/article_list_admin.html.twig', [
            'articles' => $articleRepo->findAll(),
        ]);
    }

    /**
     * permet d'afficher les détail d'un article
     * permet également d'ajouter un commentaire via le service persistComment
     * @param Article $article
     * @return Response
     * @Route("/blog/{slug}", name="article_detail", methods={"GET", "POST"})
     */
    public function articleDetail(
        Article $article,
         Request $req,
         CommentService $commentService,
         CategoryArticleRepository $categoryArtRepo,
         EntityManagerInterface $em,
         ArticleRepository $articleRepo
         ):Response
         {
             $comment = new Comments();//Créer un model de commentaire pour le formulaire
             $categoryArticle = $categoryArtRepo->find($article->getCategoryArticle());
             
             $comments = $commentService->allCommentPublished();
             $categoriesArticle = $categoryArtRepo->findAll();
             //dd($categoryArticle);

            
             $form = $this->createForm(CommentType::class, $comment);
             $form->handleRequest($req);
             if($form->isSubmitted() and $form->isValid()){
                // dd($form->getData());
                $comment = $form->getData();
                $commentService->persistComment($form->getData(), $article);

                return $this->redirectToRoute('article_detail', ['slug'=> $article->getSlug()]);
             }
            $article->setView($article->getView() + 1);   
            $em->flush() ;
            $articleOrderByView = $articleRepo->findAllByView();
        

        
        return $this->renderForm('frontoffice/blog_detail.html.twig', 
        compact(
            'article', 
            'form', 
            'comments',
            'categoriesArticle',
            'articleOrderByView',
            'categoryArticle'
        ));
    }

    /**
     * Undocumented function
     *
     * @param Comments $comment
     * @param Request $req
     * @param EntityManagerInterface $em
     * @return void
     * @Route("/blog/{slug}/comment", name="article_comment_add")
     */
    public function addComment (Article $article , Request $req, EntityManagerInterface $em){
        $validToken = $req->request->get('csrf_token');
        if($this->isCsrfTokenValid('comment', $validToken)){
            
        }
        return $this->json(['code'=>200, 'message'=>'ça marche bien!']);
    }

     /**
     * permet d'ajouter un article de blog
     *
     * @param Request $req
     * @param EntityManagerInterface $em
     * @return void
     * @Route("/admin/article_add", name="article_add", methods={"GET","POST"} )
     */
    public function addArticle(Request $req, EntityManagerInterface $em ){
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->add('imageFile', VichImageType::class,[
                'label'=>false,
                 'required'=>false,
                 'allow_delete'=>true,
                 'download_uri' => false,
                'image_uri' => true,
                'delete_label' => 'Supprimez cette image',
                "constraints"=>[
                    new NotNull(),
                ]
                ]);
        $form->handleRequest($req);
        if($form->isSubmitted() and $form->isValid()){
            $article->setCreatedAt(new \DateTime('now'));
            //dd($article);
            
            $em->persist($article);
            $em->flush();
            return $this->redirectToRoute('article_list_admin');
        }

        return $this->renderForm("backoffice/article/article_add.html.twig", compact('form'));
    }

    /**
     * Undocumented function
     *
     * @param Request $req
     * @param EntityManagerInterface $em
     * @param Article $article
     * @return Response
     * @Route("/admin/article/article_edit/{slug}", name="article_edit")
     */
    public function editArticle(Request $req, EntityManagerInterface $em, Article $article):Response{
        $form = $this->createForm(ArticleType::class, $article);
        $form->add('imageFile', VichImageType::class,[
                'label'=>false,
                 'required'=>false,
                 'allow_delete'=>true,
                 'download_uri' => false,
                'image_uri' => true,
                ]);
        $form->handleRequest($req);
        if($form->isSubmitted() and $form->isValid()){
            $em->flush();
            return $this->redirectToRoute('article_list_admin');
        }
        return $this->renderForm("backoffice/article/article_edit.html.twig", compact('form','article'));
    }

    /**
     * Undocumented function
     *
     * @param Article $article
     * @param Request $req
     * @param EntityManagerInterface $em
     * @return Response
     * @Route("/admin/article/article_delete/{slug}", name="article_delete")
     */
    public function articleDelete(Article $article, EntityManagerInterface $em):Response{
        $em->remove($article);
        $em->flush();

        return $this->redirectToRoute('article_list_admin');
    }


    /**
     * Permet de liker et de unliker
     *
     * @Route("/blog/{id}/like", name="post_like")
     * @param Article $article
     * @param EntityManagerInterface $manager
     * @param LikeRepository $likeRepo
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function like(Article $article, EntityManagerInterface $manager, LikeRepository $likeRepo):Response{
        $user = $this->getUser();
        if(!$user) return $this->json([
            'code'=>403,
            'message'=>'Unauthorized'
        ],403);

        if($article->isLikedByUser($user)){
            $like = $likeRepo->findOneBy([
                'article'=>$article,
                'user'=>$user
            ]);
            $manager->remove($like);
            $manager->flush();

            return $this->json([
                'code'=>200,
                'message'=>'Like bien supprimé',
                'likes'=>$likeRepo->count(['article'=>$article])
            ],200);
        }

        $like = new Like();
        $like->setArticle($article)
             ->setUser($user);
        $manager->persist($like);
        $manager->flush();

        return $this->json(['code'=>200,'message'=>'Like bien ajouté','likes'=>$likeRepo->count(['article'=>$article])],200);
    }

    public function newsletter(EntityManagerInterface $manager, LikeRepository $likeRepo):Response{

    }
}
