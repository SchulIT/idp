<?php

namespace App\Repository;

use App\Entity\ActiveDirectorySyncOption;
use Doctrine\ORM\EntityManagerInterface;

class ActiveDirectorySyncOptionRepository implements ActiveDirectorySyncOptionRepositoryInterface {

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em
            ->getRepository(ActiveDirectorySyncOption::class)
            ->findBy([], [
                'name' => 'asc'
            ]);
    }

    /**
     * @inheritDoc
     */
    public function persist(ActiveDirectorySyncOption $option): void {
        $this->em->persist($option);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    public function remove(ActiveDirectorySyncOption $option): void {
        $this->em->remove($option);
        $this->em->flush();
    }
}