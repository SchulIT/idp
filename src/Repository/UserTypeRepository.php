<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserType;
use Doctrine\ORM\EntityManagerInterface;

class UserTypeRepository implements UserTypeRepositoryInterface {

    private $_em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->_em = $entityManager;
    }

    public function countUsersOfUserType(UserType $userType): int {
        $qb = $this->_em->createQueryBuilder()
            ->select('COUNT(1)')
            ->from(User::class, 'u')
            ->where('u.type = :type')
            ->setParameter('type', $userType->getId());

        return $qb->getQuery()->getSingleScalarResult();
    }
}