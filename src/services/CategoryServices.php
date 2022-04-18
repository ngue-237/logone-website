<?php
namespace App\services;

use App\Repository\CategoryServiceRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;


class CategoryServices {

    private $categoryServiceRepo;
    private $paginator;
    
    public function __construct(CategoryServiceRepository $categoryServiceRepo, PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
        $this->categoryServiceRepo = $categoryServiceRepo;
    }

    public function getAllCategoryService($req){
        $categories= $this->paginator->paginate(
            $this->categoryServiceRepo->findAll(), 
            $req->query->getInt('page', 1), 
            8/*limit per page*/
        );
        return $categories;
    }
}