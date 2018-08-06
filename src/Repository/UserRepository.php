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

    public function getUsersUpdatedAfter(\DateTime $dateTime) {
        $qb = $this->_em
            ->createQueryBuilder();

        $qb->select(['DISTINCT u.id'])
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

        $result = $qb->getQuery()->getScalarResult();
        return $result;
    }

    public function findOneByUsername(string $username): ?User {
        return $this->_em->getRepository(User::class)
            ->findOneByUsername($username);
    }
}