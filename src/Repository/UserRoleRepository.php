<?php

namespace App\Repository;

use App\Entity\UserRole;
use Doctrine\ORM\EntityManagerInterface;

class UserRoleRepository implements UserRoleRepositoryInterface {

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function findAll() {
        return $this->em
            ->getRepository(UserRole::class)
            ->findBy([], [
                'name' => 'asc'
            ]);
    }

    public function persist(UserRole $role) {
        $this->em->persist($role);
        $this->em->flush();
    }

    public function remove(UserRole $role) {
        $this->em->remove($role);
        $this->em->flush();
    }
}