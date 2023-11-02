<?php

namespace App\Repository;

use App\Data\SearchMessageData;
use App\Entity\Chat;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chat>
 *
 * @method Chat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chat[]    findAll()
 * @method Chat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chat::class);
    }

    public function save(Chat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Chat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    /**
     * Returns an array of Chat objects for User1 or User2
     */
    public function findChatListForUser(User $user, $offset = 0, $limit = 100): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user1 = :user')
            ->orWhere('c.user2 = :user')
            ->setParameter(':user', $user)
            ->orderBy('c.lastMessageCreatedAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * Returns an array of Chat objects for User1 and User2
     */
    public function findChatBetweenUsers(User $user1, User $user2): ?Chat
    {
        return $this->createQueryBuilder('c')
            ->andWhere('(c.user1 = :user1 AND c.user2 = :user2) OR (c.user1 = :user2 AND c.user2 = :user1)')
            ->setParameter(':user1', $user1)
            ->setParameter(':user2', $user2)
            ->orderBy('c.lastMessageCreatedAt', 'ASC')
            ->getQuery()
            ->getResult()[0] ?? null
            ;
    }

    /**
     * Returns an array of Chat not seen for User1 or User2
     */
    public function findLastChatNoSeenByUser(User $user): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user1 = :user')
            ->orWhere('c.user2 = :user')
            ->andWhere('c.lastMessageSentBy <> :user')
            ->setParameter(':user', $user)
            ->andWhere('c.isLastMessageSeen = false')
            ->orderBy('c.lastMessageCreatedAt', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }


    public function findAllMessageNotSeenFromDays(int $offset = 0, int $limit = 100, int $days = 1): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.isLastMessageSeen = false')
            ->andWhere('DATE_DIFF(CURRENT_DATE(), c.lastMessageCreatedAt) = :days')
            ->setParameter('days', $days)
            ->orderBy('c.lastMessageCreatedAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * Returns an array of Chat by search
     */
    public function searchChat(SearchMessageData $data): array
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.user1', 'u1')
            ->leftJoin('c.user2', 'u2')
        ;

        if ($data->user1 && $data->user2) {

            $qb->where('u1.email LIKE :user1 AND u2.email LIKE :user2')
                ->orWhere('u1.email LIKE :user2 AND u2.email LIKE :user1')

                ->orWhere('u1.firstname LIKE :user1 AND u2.firstname LIKE :user2')
                ->orWhere('u1.firstname LIKE :user2 AND u2.firstname LIKE :user1')

                ->orWhere('u1.lastname LIKE :user1 AND u2.lastname LIKE :user2')
                ->orWhere('u1.lastname LIKE :user2 AND u2.lastname LIKE :user1')

                ->setParameter('user1', "%{$data->user1}%")
                ->setParameter('user2', "%{$data->user2}%");
        } elseif ($data->user1) {
            $qb->where('u1.email LIKE :user1 OR u2.email LIKE :user1')
                ->orWhere('u1.firstname LIKE :user1 OR u2.firstname LIKE :user1')
                ->orWhere('u1.lastname LIKE :user1 OR u2.lastname LIKE :user1')
                ->setParameter('user1', "%{$data->user1}%");
        } elseif ($data->user2) {
            $qb->where('u1.email LIKE :user2 OR u2.email LIKE :user2')
                ->orWhere('u1.firstname LIKE :user2 OR u2.firstname LIKE :user2')
                ->orWhere('u1.lastname LIKE :user2 OR u2.lastname LIKE :user2')
                ->setParameter('user2', "%{$data->user2}%");
        }

        if ($data->chatId) {
            $qb->where('c.id = :chatId')
                ->setParameter('chatId', $data->chatId);;
        }

        return $qb->getQuery()->getResult();
    }
}
