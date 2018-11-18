<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserRepository implements UserRepositoryInterface {

    private $_em;

    public function __construct(EntityManagerInterface $objectManager) {
        $this->_em = $objectManager;
    }

    public function findAll($offset = 0, $limit = null) {
        $qb = $this->_em
            ->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->orderBy('u.username', 'asc')
            ->setFirstResult($offset);

        if($limit !== null) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    public function findUsersByUsernames(array $usernames) {
        $qb = $this->_em->createQueryBuilder();

        $qb->select(['u', 'a', 'r', 't'])
            ->from(User::class, 'u')
            ->leftJoin('u.attributes', 'a')
            ->leftJoin('u.userRoles', 'r')
            ->leftJoin('u.type', 't')
            ->where('u.username IN (:usernames)')
            ->setParameter('usernames', $usernames);

        return $qb->getQuery()->getResult();
    }

    public function findUsersUpdatedAfter(\DateTime $dateTime, array $usernames = [ ]) {
        $qb = $this->_em
            ->createQueryBuilder();

        $qb->select(['DISTINCT u.username'])
            ->from(User::class, 'u')
            ->leftJoin('u.attributes', 'a')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->andX(
                        $qb->expr()->isNotNull('u.updatedAt'),
                        $qb->expr()->gt('u.updatedAt', ':datetime')
                    ),
                    $qb->expr()->andX(
                        $qb->expr()->isNotNull('a.updatedAt'),
                        $qb->expr()->gt('a.updatedAt', ':datetime')
                    )
                )
            )
            ->setParameter('datetime', $dateTime);

        if(count($usernames) > 0) {
            $qb->andWhere('u.username IN (:usernames)')
                ->setParameter('usernames', $usernames);
        }

        $usernames = $qb->getQuery()->getScalarResult();

        return $this->findUsersByUsernames($usernames);
    }

    public function findOneByUsername(string $username): ?User {
        $qb = $this->_em->createQueryBuilder();

        $qb->select(['u', 'a', 'r', 't'])
            ->from(User::class, 'u')
            ->leftJoin('u.attributes', 'a')
            ->leftJoin('u.userRoles', 'r')
            ->leftJoin('u.type', 't')
            ->where('u.username = :username')
            ->setParameter('username', $username);

        $result = $qb->getQuery()->getResult();

        if(count($result) === 0) {
            return null;
        }

        return $result[0];
    }
}