<?php

namespace App\Entity\Myflix;

use App\Repository\Myflix\VideoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VideoRepository::class)]
class Video
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $localeSrc = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $uqloadSrc = null;

    #[ORM\Column(nullable: true)]
    private ?int $ratings = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLocaleSrc(): ?string
    {
        return $this->localeSrc;
    }

    public function setLocaleSrc(?string $localeSrc): static
    {
        $this->localeSrc = $localeSrc;

        return $this;
    }

    public function getUqloadSrc(): ?string
    {
        return $this->uqloadSrc;
    }

    public function setUqloadSrc(?string $uqloadSrc): static
    {
        $this->uqloadSrc = $uqloadSrc;

        return $this;
    }

    public function getRatings(): ?int
    {
        return $this->ratings;
    }

    public function setRatings(?int $ratings): static
    {
        $this->ratings = $ratings;

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
}
