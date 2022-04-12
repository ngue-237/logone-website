<?php

namespace App\Twig;

use App\Repository\CategoryArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;


class CategoryArticleExtension extends AbstractExtension{
    private CategoryArticleRepository $categoryArticleRepo;

    public function __construct(
        CategoryArticleRepository $categoryArticleRepo
    )
    {
        $this->categoryArticleRepo = $categoryArticleRepo;
    }

    public function getFunctions() : array
    {
        return [
            new TwigFunction("catgsArticle", [$this, "getCategoriesArticle"])
        ];
    }

    public function getCategoriesArticle(){
        return $this->categoryArticleRepo->findAllByDateF();
    }
}