<?php

namespace App\Repository;

use App\Entity\UserRole;
use Doctrine\ORM\EntityManagerInterface;

class UserRoleRepository implements UserRoleRepositoryInterface {

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em
            ->getRepository(UserRole::class)
            ->findBy([], [
                'name' => 'asc'
            ]);
    }

    public function persist(UserRole $role): void {
        $this->em->persist($role);
        $this->em->flush();
    }

    public function remove(UserRole $role): void {
        $this->em->remove($role);
        $this->em->flush();
    }
}