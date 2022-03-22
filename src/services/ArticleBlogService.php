<?php
namespace App\services;

use App\Repository\ArticleRepository;

Class ArticleBlogService{

    private $articleBlogRepo;

    public function _construct(ArticleRepository $articleBlogRepo){
        $this->articleBlogRepo = $articleBlogRepo;
    }
    
    public function ArticleBlog(){
        return $this->articleBlogRepo->findAll();
    }
}