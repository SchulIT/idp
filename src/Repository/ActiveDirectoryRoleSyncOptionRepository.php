<?php

namespace App\Repository;

use App\Entity\ActiveDirectoryRoleSyncOption;
use Doctrine\ORM\EntityManagerInterface;

class ActiveDirectoryRoleSyncOptionRepository implements ActiveDirectoryRoleSyncOptionRepositoryInterface {

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function findAll() {
        return $this->em
            ->getRepository(ActiveDirectoryRoleSyncOption::class)
            ->findBy([], [
                'name' => 'asc'
            ]);
    }

    /**
     * @inheritDoc
     */
    public function persist(ActiveDirectoryRoleSyncOption $option) {
        $this->em->persist($option);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    public function remove(ActiveDirectoryRoleSyncOption $option) {
        $this->em->remove($option);
        $this->em->flush();
    }
}