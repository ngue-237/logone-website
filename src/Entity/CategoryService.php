<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Sluggable\Util\Urlizer;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\Collection;
use App\Repository\CategoryServiceRepository;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=CategoryServiceRepository::class)
 *  @Vich\Uploadable
 */
class CategoryService
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
    private $designation;
    /**
     * @Gedmo\Slug(fields={"designation"})
     * @ORM\Column(length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=Service::class, mappedBy="category")
     */
    private $services;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

     /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="category_service_images", fileNameProperty="image")
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $updatedAt ;

    // /**
    //  * @ORM\OneToMany(targetEntity=Images::class, mappedBy="categoryService", orphanRemoval=true, cascade={"persist"})
    //  */
    // private $images;

    /**
     * @ORM\OneToMany(targetEntity=Devis::class, mappedBy="categories")
     */
    private $devis;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        // $this->images = new ArrayCollection();
        $this->devis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): self
    {
        $this->designation = $designation;

        return $this;
    }

    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
            $service->setCategory($this);
        }

        return $this;
    }

    public function removeService(Service $service): self
    {
        if ($this->services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getCategory() === $this) {
                $service->setCategory(null);
            }
        }

        return $this;
    }

     public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($image) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    // /**
    //  * @return Collection<int, Images>
    //  */
    // public function getImages(): Collection
    // {
    //     return $this->images;
    // }

    // public function addImage(Images $image): self
    // {
    //     if (!$this->images->contains($image)) {
    //         $this->images[] = $image;
    //         $image->setCategoryService($this);
    //     }

    //     return $this;
    // }

    // public function removeImage(Images $image): self
    // {
    //     if ($this->images->removeElement($image)) {
    //         // set the owning side to null (unless already changed)
    //         if ($image->getCategoryService() === $this) {
    //             $image->setCategoryService(null);
    //         }
    //     }

    //     return $this;
    // }

    /**
     * @return Collection<int, Devis>
     */
    public function getDevis(): Collection
    {
        return $this->devis;
    }

    public function addDevi(Devis $devi): self
    {
        if (!$this->devis->contains($devi)) {
            $this->devis[] = $devi;
            $devi->setCategories($this);
        }

        return $this;
    }

    public function removeDevi(Devis $devi): self
    {
        if ($this->devis->removeElement($devi)) {
            // set the owning side to null (unless already changed)
            if ($devi->getCategories() === $this) {
                $devi->setCategories(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of slug
     */ 
    public function getSlug()
    {
        if (!$this->slug) {
            return Urlizer::urlize($this->getDesignation());
        }

        return $this->slug;
    }
}
