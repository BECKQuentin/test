<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Validator\PhoneNumber;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'Il y a déjà un compte avec cet email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    const ROLE_ADMIN        = 'Administrateur';
    const MARKING_PASSWORD_REQUESTED = 'password-requested';
    const MARKING_EMAIL_VERIFICATION = 'email-verification';
    const MARKING_ACTIVE = 'active';
    const MARKING_BLOCKED = 'blocked';
    const MARKING_DELETED = 'deleted';
    const MARKINGS = [self::MARKING_PASSWORD_REQUESTED, self::MARKING_EMAIL_VERIFICATION, self::MARKING_ACTIVE, self::MARKING_BLOCKED, self::MARKING_DELETED];



    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['basic', 'chat_list', 'chat_message'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['basic'])]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\Column(length: 255)]
    private ?string $marking = self::MARKING_PASSWORD_REQUESTED;


    #[Assert\NotBlank]
    #[Assert\Length([
        'min' => 2,
        'max' => 255,
        'minMessage' => 'Votre prénom doit être plus long',
        'maxMessage' => 'Votre prénom de peut pas dépasser 255 caractères'
    ])]
    #[ORM\Column(length: 255)]
    #[Groups(['basic'])]
    private ?string $firstname = null;

    #[Assert\NotBlank]
    #[Assert\Length([
        'min' => 2,
        'max' => 255,
        'minMessage' => 'Votre prénom doit être plus long',
        'maxMessage' => 'Votre prénom de peut pas dépasser 255 caractères'
    ])]
    #[ORM\Column(length: 255)]
    #[Groups(['basic'])]
    private ?string $lastname = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[PhoneNumber]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[PhoneNumber]
    private ?string $mobile = null;


    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deletedAt = null;

    #[ORM\Column]
    private ?bool $isAgreeRGPD = false;

    #[ORM\Column(length: 255)]
    private ?string $locale = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    #[Groups(['basic'])]
    private ?UserCoordinates $coordinates = null;

    public function __construct()
    {
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTimeImmutable('now'));
        }
    }

    #[Groups(['basic', 'chat_list', 'chat_message'])]
    public function getFullName(): string
    {
        return ucfirst($this->firstname) . ' ' . strtoupper($this->lastname);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(string $role): self
    {
        $this->roles[] = $role;
        $this->roles = array_values(array_unique($this->roles));

        return $this;
    }

    public function removeRole(string $role): self
    {
        $this->roles = array_diff($this->roles, [$role]);
        $this->roles = array_values(array_unique($this->roles));

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getMarking(): ?string
    {
        return $this->marking;
    }

    public function setMarking(string $marking): static
    {
        $this->marking = $marking;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(?string $mobile): static
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function isIsAgreeRGPD(): ?bool
    {
        return $this->isAgreeRGPD;
    }

    public function setIsAgreeRGPD(bool $isAgreeRGPD): static
    {
        $this->isAgreeRGPD = $isAgreeRGPD;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    public function getCoordinates(): ?UserCoordinates
    {
        return $this->coordinates;
    }

    public function setCoordinates(?UserCoordinates $coordinates): static
    {
        // unset the owning side of the relation if necessary
        if ($coordinates === null && $this->coordinates !== null) {
            $this->coordinates->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($coordinates !== null && $coordinates->getUser() !== $this) {
            $coordinates->setUser($this);
        }

        $this->coordinates = $coordinates;

        return $this;
    }
}
