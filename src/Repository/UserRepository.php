<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository {
    public function getUsersUpdatedAfter(\DateTime $dateTime) {
        $qb = $this->_em
            ->createQueryBuilder();

        $qb->select(['DISTINT u.id'])
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
}