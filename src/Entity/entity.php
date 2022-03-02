<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     min=4,
     *     max=50,
     *     minMessage="Your lastname must be at least {{ limit }} characters long",
     *     maxMessage = "Your lastname cannot be longer than {{ limit }} characters"
     * )
     * * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Your firstname cannot contain a number"
     * )
     */
    private $lastName;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *  @Assert\Length(
     *     min=4,
     *     max=50,
     *     minMessage="Your first name must be at least {{ limit }} characters long",
     *     maxMessage = "Your first name cannot be longer than {{ limit }} characters"
     * )
     * * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Your firstname cannot contain a number"
     * )
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     min="8",
     *     minMessage="Votre mot de passe  doit faire au moins {{ limit }} caractères"
     * )
     */
    private $password;

    /**
     * @Assert\EqualTo(
     *     propertyPath="password",
     *     message="Le mot de passe doit être identique"
     * )
     */
    private $passwordConfirm;

    /**
     * @ORM\Column(type="json")
     */
    private $role = [];

    /**
     * @ORM\Column(type="date")
     */
    private $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPasswordConfirm(): ?string
    {
        return $this->passwordConfirm;
    }

    public function setPasswordConfirm(?string $passwordConfirm): self
    {
        $this->passwordConfirm = $passwordConfirm;

        return $this;
    }

    public function getRole(): ?array
    {
        return $this->role;
    }

    public function setRole(array $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
