<?php

namespace App\Repository;

use App\Data\SearchUserData;
use App\Entity\User;
use App\Entity\UserCoordinates;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    //////////////////////ADD GLOBALS METHOD//////////////////////////////////
//    private function addActiveUser(QueryBuilder $qb): void
//    {
//        $qb ->andWhere("u.marking LIKE :active")
//            ->setParameter('active', User::MARKING_ACTIVE);
//    }

    private function addNoDeleteUser(QueryBuilder $qb): void
    {
        $qb->andWhere('u.deletedAt IS NULL');
    }
    /////////////////////////////////////////////////////////////////////////////





    public function findNoDeletedOrderByCreatedAt(string $order = 'asc'): array
    {
        $qb = $this->createQueryBuilder('u');

        $this->addNoDeleteUser($qb);

        if ($order === 'asc') {
            $qb->orderBy('u.createdAt', 'ASC');
        } else if ($order === 'desc') {
            $qb->orderBy('u.createdAt', 'DESC');
        }

        return $qb->getQuery()->getResult();
    }


    public function findNearest(float $longitude, float $latitude, User $currentUser, int $maxDistance = 100)
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u')
            ->leftJoin('u.coordinates', 'c')

            ->addSelect('(6371 * ACOS(COS(RADIANS(:latitude)) * COS(RADIANS(c.latitude)) * COS(RADIANS(c.longitude) - RADIANS(:longitude)) + SIN(RADIANS(:latitude)) * SIN(RADIANS(c.latitude))) ) AS distance')
            ->setParameter('latitude', $latitude)
            ->setParameter('longitude', $longitude)
            ->having('distance <= :max_distance')
            ->setParameter('max_distance', $maxDistance)
            ->andWhere('u.id != :current_user_id')
            ->setParameter('current_user_id', $currentUser->getId())
            ->orderBy('distance', 'ASC');

//
//        ->select('name', 'latitude', 'longitude', 'region', DB::raw(sprintf(
//        '(6371 * acos(cos(radians(%1$.7f)) * cos(radians(latitude)) * cos(radians(longitude) - radians(%2$.7f)) + sin(radians(%1$.7f)) * sin(radians(latitude)))) AS distance',
//        $request->input('latitude'),
//        $request->input('longitude')
//    )))
//        ->having('distance', '<', 50)


        return $qb->getQuery()->getResult();
    }





    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }



    public function searchUser(SearchUserData $searchData): array
    {
        $qb = $this->createQueryBuilder('u');

        $qb->orWhere('u.email LIKE :q')
            ->orWhere('u.firstname LIKE :q')
            ->orWhere('u.lastname LIKE :q');

        $qb->leftJoin('u.installer', 'i');
        $qb->leftJoin('u.contractor', 'c');

        $qb->orWhere(
            $qb->expr()->andX(
                $qb->expr()->isNotNull('i'),
                $qb->expr()->orX(
                    $qb->expr()->eq('i.numContratCadre', ':qExact'),
                    $qb->expr()->eq('i.numSiret', ':qExact'),
                    $qb->expr()->eq('i.postalCode', ':qExact'),
                    $qb->expr()->like('i.socialReason', ':q'),
                    $qb->expr()->like('i.city', ':q'),
                ),

            )
        );

        $qb->orWhere(
            $qb->expr()->andX(
                $qb->expr()->isNotNull('c'),
                $qb->expr()->orX(
                    $qb->expr()->eq('c.numContratCadre', ':qExact'),
                    $qb->expr()->eq('c.numSiret', ':qExact'),
                    $qb->expr()->eq('c.postalCode', ':qExact'),
                    $qb->expr()->like('c.socialReason', ':q'),
                    $qb->expr()->like('c.city', ':q'),
                ),
            )
        );



//        $qb->andWhere(
//            $qb->expr()->orX(
//                $qb->expr()->isNotNull('c'),
//                $qb->expr()->eq('c.numSiret', ':qExact')
//            )
//        );

        $qb->setParameter('q', "%{$searchData->q}%");
        $qb->setParameter('qExact', $searchData->q);

//        if ($searchData->numSiret) {
//            $qb->andWhere('u.numSiret = :numSiret')
//                ->setParameter('numSiret', $searchData->numSiret);
//        }

//        if($searchData->isAccountVerified) {
//            $qb->andWhere('u.isAccountVerified = 1');
//        }
//
//        if($searchData->locale) {
//            $qb->andWhere('u.locale = :locale')
//                ->setParameter('locale', $searchData->locale);
//        }



//        dd($qb->getQuery());

        $this->addNoDeleteUser($qb);
        return $qb->getQuery()->getResult();
    }
}
