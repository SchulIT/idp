<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserType;
use Doctrine\ORM\EntityManagerInterface;

class UserTypeRepository implements UserTypeRepositoryInterface {

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function countUsersOfUserType(UserType $userType): int {
        $qb = $this->em->createQueryBuilder()
            ->select('COUNT(1)')
            ->from(User::class, 'u')
            ->where('u.type = :type')
            ->setParameter('type', $userType->getId());

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findAll(): array {
        return $this->em->getRepository(UserType::class)
            ->findBy([], [
                'name' => 'asc'
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findAllUuids(): array {
        return array_map(function(array $item) {
            return $item['uuid'];
        },
            $this->em->createQueryBuilder()
            ->select('u.uuid')
            ->from(UserType::class, 'u')
            ->getQuery()
            ->getScalarResult());
    }

    public function persist(UserType $userType): void {
        $this->em->persist($userType);
        $this->em->flush();
    }

    public function remove(UserType $userType): void {
        $this->em->remove($userType);
        $this->em->flush();
    }

    public function findOneByUuid(string $uuid): ?UserType {
        return $this->em->getRepository(UserType::class)
            ->findOneBy(['uuid' => $uuid]);
    }

    public function findOneByAlias(string $alias): ?UserType {
        return $this->em->getRepository(UserType::class)
            ->findOneBy(['alias' => $alias]);
    }
}