<?php
namespace App\Twig;

use App\Repository\CategoryServiceRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CategoryServiceExtention extends AbstractExtension{

private $categoryServiceRepo;

public function __construct(CategoryServiceRepository $categoryServiceRepo)
{
    $this->categoryServiceRepo = $categoryServiceRepo;
}

public function getFunctions() : array
{
    return [
        new TwigFunction("catgServ", [$this, "getCategoriesService"])
    ];
}

public function getCategoriesService(){
    return $this->categoryServiceRepo->findAllByDateF();
}

}
