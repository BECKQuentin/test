<?php

namespace App\Manager;

use App\Entity\Chat;
use App\Entity\ChatMessage;
use App\Entity\User;
use App\Exception\AppException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ChatMessageManager
{

    public function __construct(
        private EntityManagerInterface              $entityManager,
        private UrlGeneratorInterface               $urlGenerator,
    ){}

    /**
     * @throws AppException
     */
    public function createConversation(User $user1, User $user2): Chat
    {
        if ($user1 === $user2) {
            throw new AppException('Cannot create conversation between same User');
        }

        $chat = (new Chat())
            ->setUser1($user1)
            ->setUser2($user2)
        ;
        $this->entityManager->persist($chat);
        $this->entityManager->flush();
        return $chat;
    }

    public function createMessage(Chat $chat, User $user, string $content): ChatMessage
    {

        $chatMessage = (new ChatMessage())
            ->setChat($chat)
            ->setSender($user)
            ->setContent($content)
        ;
        $chat->setLastMessageSentBy($user);
        $chat->setLastMessageCreatedAt(new \DateTimeImmutable('NOW'));
        $chat->setLastMessageAbstract(mb_substr($content, 0, 15) . '...');
        $chat->setIsLastMessageSeen(false);

        $this->entityManager->persist($chatMessage);
        $this->entityManager->flush();
        return $chatMessage;
    }

    public function readMessage(Chat $chat, User $user): void
    {
        if ($chat->getLastMessageSentBy() !== $user) {
            $chat->setIsLastMessageSeen(true);
        }
        foreach ($chat->getChatMessages() as $message) {
            if ($message->getReadAt() === null && $message->getSender() !== $user) {
                $message->setReadAt(new \DateTimeImmutable('NOW'));
            }
        }
        $this->entityManager->flush();
    }

    public function hasParticipant(Chat $chat, User $user): bool
    {
        if ($chat->getUser1() === $user || $chat->getUser2() === $user) {
            return true;
        }
        return false;
    }

    public function sendEmailByChat(Chat $chat, string $template): void
    {
        $user = $chat->getLastMessageSentBy() === $chat->getUser1() ? $chat->getUser2() : $chat->getUser1();
        $sender = $chat->getLastMessageSentBy() === $chat->getUser2() ? $chat->getUser2() : $chat->getUser1();


        $context = [
            'user1'     => [
                'fullname' => $user->getFullName(),
            ],
            'user2'     => [
                'fullname'  => $sender->getFullName(),
                'url' => $this->urlGenerator->generate('user_show', ['id' => $sender->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            ],
            'chat' => [
                'url'   => $this->urlGenerator->generate('user_messages', ['chat' => $chat->getId()],UrlGeneratorInterface::ABSOLUTE_URL),
            ]
        ];

//        $email = (new Email())
//            ->setTemplateId($this->emailMailjetTemplateRepository->getTemplateId($template, $user->getLocale()))
//            ->setToUser($user)
//            ->setContext($context);
//
//        $this->emailService->sendEmail($email);
    }

}