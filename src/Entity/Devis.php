<?php

namespace App\Entity;

use App\Repository\DevisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DevisRepository::class)
 */
class Devis
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *@ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     
     * 
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $company;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $country;

    /**
     * @ORM\Column(type="text")
     */
    private $subject;

    /**
     * @ORM\Column(type="date")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=CategoryService::class, inversedBy="devis")
     * @ORM\JoinColumn(nullable=false)
     */
    private $categories;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $rgpd;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $confirm;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $closingStatus;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $jobDone;

    /**
     * @ORM\ManyToOne(targetEntity=Service::class, inversedBy="devis")
     * 
     */
    private $services;   

    
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt = new \DateTime('now');
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCategories(): ?CategoryService
    {
        return $this->categories;
    }

    public function setCategories(?CategoryService $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    public function getRgpd(): ?bool
    {
        return $this->rgpd;
    }

    public function setRgpd(?bool $rgpd): self
    {
        $this->rgpd = $rgpd;

        return $this;
    }

    public function getConfirm(): ?string
    {
        return $this->confirm;
    }

    public function setConfirm(?string $confirm): self
    {
        $this->confirm = $confirm;

        return $this;
    }

    public function getClosingStatus(): ?bool
    {
        return $this->closingStatus;
    }

    public function setClosingStatus(?bool $closingStatus): self
    {
        $this->closingStatus = $closingStatus;

        return $this;
    }

    public function getJobDone(): ?bool
    {
        return $this->jobDone;
    }

    public function setJobDone(?bool $jobDone): self
    {
        $this->jobDone = $jobDone;

        return $this;
    }

    public function getServices(): ?Service
    {
        return $this->services;
    }

    public function setServices(?Service $services): self
    {
        $this->services = $services;

        return $this;
    }

   

    

    
}
