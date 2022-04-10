<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Sluggable\Util\Urlizer;
use App\Repository\ArticleRepository;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File\UploadedFIle;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 *  @Vich\Uploadable
 *  @UniqueEntity(
 *     fields = {"title"},
 *     message="This title already exist"
 * )
 */
class Article
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
    private $title;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="article_images", fileNameProperty="image")
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=Comments::class, mappedBy="article", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $author;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=CategoryArticle::class, inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $categoryArticle;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $view;

    /**
     * @ORM\OneToMany(targetEntity=Like::class, mappedBy="article")
     */
    private $likes;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isPublished;

    /**
     * @ORM\ManyToOne(targetEntity=CategoryService::class, inversedBy="articles")
     */
    private $categoryService;

    

    

    public function __construct()
    {
        
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
         
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the value of slug
     */ 
    public function getSlug()
    {
        if (!$this->slug) {
            return Urlizer::urlize($this->getTitle());
        }
        return $this->slug;
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

    /**
     * @return Collection<int, Comments>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setArticle($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCategoryArticle(): ?CategoryArticle
    {
        return $this->categoryArticle;
    }

    public function setCategoryArticle(?CategoryArticle $categoryArticle): self
    {
        $this->categoryArticle = $categoryArticle;

        return $this;
    }

    public function getView(): ?int
    {
        return $this->view;
    }

    public function setView(?int $view): self
    {
        $this->view = $view;

        return $this;
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setArticle($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getArticle() === $this) {
                $like->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * Permet de savoir si cet article est liké par un user
     *
     * @param User $user
     * @return boolean
     */
   public function isLikedByUser(User $user):bool{
        foreach ($this->likes as $like) {
            if ($like->getUser() === $user) {
                return true;
            }
        }
        return false;
   }

   public function getIsPublished(): ?bool
   {
       return $this->isPublished;
   }

   public function setIsPublished(?bool $isPublished): self
   {
       $this->isPublished = $isPublished;

       return $this;
   }

   public function getCategoryService(): ?CategoryService
   {
       return $this->categoryService;
   }

   public function setCategoryService(?CategoryService $categoryService): self
   {
       $this->categoryService = $categoryService;

       return $this;
   }
   


     
}
