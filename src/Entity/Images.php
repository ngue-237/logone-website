<?php

namespace App\Entity;

use App\Repository\ImagesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImagesRepository::class)
 */
class Images
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    // /**
    //  * @ORM\ManyToOne(targetEntity=CategoryService::class, inversedBy="images")
    //  * @ORM\JoinColumn(nullable=true)
    //  */
    // private $categoryService;

    
   

   
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    // public function getCategoryService(): ?CategoryService
    // {
    //     return $this->categoryService;
    // }

    // public function setCategoryService(?CategoryService $categoryService): self
    // {
    //     $this->categoryService = $categoryService;

    //     return $this;
    // }

    

    

}
