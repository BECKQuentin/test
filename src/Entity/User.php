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

    const ROLE_CONSULTANT   = 'Consultant';
    const ROLE_CONTRACTOR   = 'Donneur d\'ordre';
    const ROLE_INSTALLER    = 'Installateur';
    const ROLE_ADMIN        = 'Administrateur';
    const ROLES = [self::ROLE_CONSULTANT, self::ROLE_CONTRACTOR, self::ROLE_INSTALLER];
    const LIST_UNIQUE_ROLES = ['ROLE_CONSULTANT', 'ROLE_INSTALLER', 'ROLE_CONTRACTOR'];


    const MARKING_PASSWORD_REQUESTED = 'password-requested';
    const MARKING_EMAIL_VERIFICATION = 'email-verification';
    const MARKING_ACTIVE = 'active';
    const MARKING_BLOCKED = 'blocked';
    const MARKING_DELETED = 'deleted';
    const MARKINGS = [self::MARKING_PASSWORD_REQUESTED, self::MARKING_EMAIL_VERIFICATION, self::MARKING_ACTIVE, self::MARKING_BLOCKED, self::MARKING_DELETED];



    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['admin_index'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['admin_index'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['admin_index'])]
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
    #[Groups(['admin_index'])]
    private ?string $firstname = null;

    #[Assert\NotBlank]
    #[Assert\Length([
        'min' => 2,
        'max' => 255,
        'minMessage' => 'Votre prénom doit être plus long',
        'maxMessage' => 'Votre prénom de peut pas dépasser 255 caractères'
    ])]
    #[ORM\Column(length: 255)]
    #[Groups(['admin_index'])]
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

    #[ORM\OneToOne(inversedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Contractor $contractor = null;

    #[ORM\OneToOne(inversedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Installer $installer = null;

    #[ORM\OneToOne(inversedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Consultant $consultant = null;

    public function __construct()
    {
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTimeImmutable('now'));
        }
    }

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

    public function getContractor(): ?Contractor
    {
        return $this->contractor;
    }

    public function setContractor(?Contractor $contractor): static
    {
        $this->contractor = $contractor;

        return $this;
    }

    public function getInstaller(): ?Installer
    {
        return $this->installer;
    }

    public function setInstaller(?Installer $installer): static
    {
        $this->installer = $installer;

        return $this;
    }

    public function getConsultant(): ?Consultant
    {
        return $this->consultant;
    }

    public function setConsultant(?Consultant $consultant): static
    {
        $this->consultant = $consultant;

        return $this;
    }
}
