<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Repository\OffreEmploiRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=OffreEmploiRepository::class)
 * @Vich\Uploadable
 */
class OffreEmploi
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $titre;

    /**
     * @ORM\Column(type="smallint")
     */
    private $nombrePoste;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_debut;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_expiration;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $max_salary;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $min_salary;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $location;


    /**
     * @ORM\ManyToMany(targetEntity=Candidature::class, inversedBy="offreEmplois")
     */
    private $candidatures;

    /**
     * @ORM\Column(type="string", length=125, nullable=true)
     */
    private $niveauScolaire;

    public function __construct()
    {
        $this->candidatures = new ArrayCollection();
    }

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $file;

    /**
     * @Vich\UploadableField(mapping="offre_pdf", fileNameProperty="file")
     * @var File
     */
    private $fileFile;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $updatedAt;

    // ...

    public function setFileFile(File $file = null)
    {
        $this->fileFile = $file;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($file) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getFileFile(): ?File
    {
        return $this->fileFile;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getNombrePoste(): ?int
    {
        return $this->nombrePoste;
    }

    public function setNombrePoste(int $nombrePoste): self
    {
        $this->nombrePoste = $nombrePoste;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(?\DateTimeInterface $date_debut): self
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDate_Expiration(): ?\DateTimeInterface
    {
        return $this->date_expiration;
    }
    public function getDateExpiration(): ?\DateTimeInterface
    {
        return $this->date_expiration;
    }

    public function setDateExpiration(?\DateTimeInterface $date_expiration): self
    {
        $this->date_expiration = $date_expiration;

        return $this;
    }

    public function getMax_Salary(): ?int
    {
        return $this->max_salary;
    }
    public function getMaxSalary(): ?int
    {
        return $this->max_salary;
    }

    public function setMaxSalary(?int $max_salary): self
    {
        $this->max_salary = $max_salary;

        return $this;
    }

    public function getMin_Salary(): ?int
    {
        return $this->min_salary;
    }
    public function getMinSalary(): ?int
    {
        return $this->min_salary;
    }

    public function setMinSalary(?int $min_salary): self
    {
        $this->min_salary = $min_salary;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return Collection<int, Candidature>
     */
    public function getCandidatures(): Collection
    {
        return $this->candidatures;
    }

    public function addCandidature(Candidature $candidature): self
    {
        if (!$this->candidatures->contains($candidature)) {
            $this->candidatures[] = $candidature;
        }

        return $this;
    }

    public function removeCandidature(Candidature $candidature): self
    {
        $this->candidatures->removeElement($candidature);

        return $this;
    }

    public function getNiveauScolaire(): ?string
    {
        return $this->niveauScolaire;
    }

    public function setNiveauScolaire(?string $niveauScolaire): self
    {
        $this->niveauScolaire = $niveauScolaire;

        return $this;
    }
}
