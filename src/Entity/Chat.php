<?php

namespace App\Entity;

use App\Repository\ChatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ChatRepository::class)]
class Chat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['chat_list', 'chat_message'])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['chat_list'])]
    private ?User $user1 = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['chat_list'])]
    private ?User $user2 = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['chat_list'])]
    private ?\DateTimeImmutable $lastMessageCreatedAt = null;

    #[ORM\ManyToOne]
    #[Groups(['chat_list'])]
    private ?User $lastMessageSentBy = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['chat_list'])]
    private ?bool $isLastMessageSeen = null;

    #[ORM\OneToMany(mappedBy: 'chat', targetEntity: ChatMessage::class, orphanRemoval: true)]
    #[Groups(['chat_list'])]
    private Collection $chatMessages;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['chat_list'])]
    private ?string $lastMessageAbstract = null;

    public function __construct()
    {
        $this->chatMessages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser1(): ?User
    {
        return $this->user1;
    }

    public function setUser1(?User $user1): static
    {
        $this->user1 = $user1;

        return $this;
    }

    public function getUser2(): ?User
    {
        return $this->user2;
    }

    public function setUser2(?User $user2): static
    {
        $this->user2 = $user2;

        return $this;
    }

    public function getLastMessageCreatedAt(): ?\DateTimeImmutable
    {
        return $this->lastMessageCreatedAt;
    }

    public function setLastMessageCreatedAt(?\DateTimeImmutable $lastMessageCreatedAt): static
    {
        $this->lastMessageCreatedAt = $lastMessageCreatedAt;

        return $this;
    }

    public function getLastMessageSentBy(): ?User
    {
        return $this->lastMessageSentBy;
    }

    public function setLastMessageSentBy(?User $lastMessageSentBy): static
    {
        $this->lastMessageSentBy = $lastMessageSentBy;

        return $this;
    }

    public function isIsLastMessageSeen(): ?bool
    {
        return $this->isLastMessageSeen;
    }

    public function setIsLastMessageSeen(bool $isLastMessageSeen): static
    {
        $this->isLastMessageSeen = $isLastMessageSeen;

        return $this;
    }

    /**
     * @return Collection<int, ChatMessage>
     */
    public function getChatMessages(): Collection
    {
        return $this->chatMessages;
    }

    public function addChatMessage(ChatMessage $chatMessage): static
    {
        if (!$this->chatMessages->contains($chatMessage)) {
            $this->chatMessages->add($chatMessage);
            $chatMessage->setChat($this);
        }

        return $this;
    }

    public function removeChatMessage(ChatMessage $chatMessage): static
    {
        if ($this->chatMessages->removeElement($chatMessage)) {
            // set the owning side to null (unless already changed)
            if ($chatMessage->getChat() === $this) {
                $chatMessage->setChat(null);
            }
        }

        return $this;
    }

    public function getLastMessageAbstract(): ?string
    {
        return $this->lastMessageAbstract;
    }

    public function setLastMessageAbstract(?string $lastMessageAbstract): static
    {
        $this->lastMessageAbstract = $lastMessageAbstract;

        return $this;
    }
}
