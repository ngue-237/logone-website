<?php

namespace App\Entity;

use App\Repository\OffreEmploiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OffreEmploiRepository::class)
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
     * @ORM\ManyToMany(targetEntity=Candidat::class, inversedBy="offreEmplois")
     */
    private $candidats;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbPoste;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbAnneeExperience;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $location;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $file;

    /**
     * @ORM\ManyToMany(targetEntity=NiveauScolaire::class, inversedBy="offreEmplois")
     */
    private $niveaux;

    public function __construct()
    {
        $this->candidats = new ArrayCollection();
        $this->niveaux = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Candidat>
     */
    public function getCandidats(): Collection
    {
        return $this->candidats;
    }

    public function addCandidats(Candidat $candidats): self
    {
        if (!$this->candidats->contains($candidats)) {
            $this->candidats[] = $candidats;
        }

        return $this;
    }

    public function removeCandidats(Candidat $candidats): self
    {
        $this->candidats->removeElement($candidats);

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getNbPoste(): ?int
    {
        return $this->nbPoste;
    }

    public function setNbPoste(int $nbPoste): self
    {
        $this->nbPoste = $nbPoste;

        return $this;
    }

    public function getNbAnneeExperience(): ?int
    {
        return $this->nbAnneeExperience;
    }

    public function setNbAnneeExperience(int $nbAnneeExperience): self
    {
        $this->nbAnneeExperience = $nbAnneeExperience;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
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
     * @return Collection<int, NiveauScolaire>
     */
    public function getNiveaux(): Collection
    {
        return $this->niveaux;
    }

    public function addNiveau(NiveauScolaire $niveau): self
    {
        if (!$this->niveaux->contains($niveau)) {
            $this->niveaux[] = $niveau;
        }

        return $this;
    }

    public function removeNiveau(NiveauScolaire $niveau): self
    {
        $this->niveaux->removeElement($niveau);

        return $this;
    }
}
