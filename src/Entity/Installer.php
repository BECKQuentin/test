<?php

namespace App\Entity;

use App\Repository\InstallerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstallerRepository::class)]
class Installer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $numContratCadre = null;

    #[ORM\Column(length: 255)]
    private ?string $socialReason = null;

    #[ORM\Column(length: 255)]
    private ?string $numSiret = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    private ?string $postalCode = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastEmailSentAt = null;

    #[ORM\OneToOne(mappedBy: 'installer', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'installer')]
    private ?Contractor $contractor = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumContratCadre(): ?string
    {
        return $this->numContratCadre;
    }

    public function setNumContratCadre(string $numContratCadre): static
    {
        $this->numContratCadre = $numContratCadre;

        return $this;
    }

    public function getSocialReason(): ?string
    {
        return $this->socialReason;
    }

    public function setSocialReason(string $socialReason): static
    {
        $this->socialReason = $socialReason;

        return $this;
    }

    public function getNumSiret(): ?string
    {
        return $this->numSiret;
    }

    public function setNumSiret(string $numSiret): static
    {
        $this->numSiret = $numSiret;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getLastEmailSentAt(): ?\DateTimeInterface
    {
        return $this->lastEmailSentAt;
    }

    public function setLastEmailSentAt(?\DateTimeInterface $lastEmailSentAt): static
    {
        $this->lastEmailSentAt = $lastEmailSentAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setInstaller(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getInstaller() !== $this) {
            $user->setInstaller($this);
        }

        $this->user = $user;

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
}
