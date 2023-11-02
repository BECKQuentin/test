<?php

namespace App\Repository;

use App\Entity\Chat;
use App\Entity\ChatMessage;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<ChatMessage>
 *
 * @method ChatMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChatMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChatMessage[]    findAll()
 * @method ChatMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatMessage::class);
    }

    public function save(ChatMessage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ChatMessage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Returns an array of Chat objects for User1 and User2
     */
    public function findMessageByChatId(string $id, $offset = 0, $limit = 7): array
    {
        return $this->createQueryBuilder('cm')
            ->andWhere('cm.chat = :chat')
            ->setParameter('chat', $id)
            ->orderBy('cm.createdAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * Returns an array of ChatMessage for id and not seen with not User of message
     *
     * @return ChatMessage[]
     */
    public function findLastMessageNoSeenForUser(Chat $chat, User $user): array
    {
        return $this->createQueryBuilder('cm')
            ->andWhere('cm.readAt IS NULL')
            ->andWhere('cm.chat = :chat')
            ->setParameter(':chat', $chat)
            ->andWhere('cm.sender <> :sender')
            ->setParameter(':sender', $user)
            ->orderBy('cm.createdAt', 'DESC')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult()
            ;
    }

    public function countUnreadMessages(User $user): int
    {
        try {
            return $this->createQueryBuilder('e')
                ->select('COUNT(e.id)')
                ->innerJoin('e.chat', 'chat')
                ->andWhere('chat.user1 = :user OR chat.user2 = :user')
                ->andWhere('e.sender != :sender')
                ->andWhere('e.readAt IS NULL')
                ->setParameter('sender', $user)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (Exception $e) {
            return 0;
        }
    }
}