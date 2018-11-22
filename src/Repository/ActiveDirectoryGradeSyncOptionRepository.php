<?php

namespace App\Repository;

use App\Entity\ActiveDirectoryGradeSyncOption;
use Doctrine\ORM\EntityManagerInterface;

class ActiveDirectoryGradeSyncOptionRepository implements ActiveDirectoryGradeSyncOptionRepositoryInterface {

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function findAll() {
        return $this->em
            ->getRepository(ActiveDirectoryGradeSyncOption::class)
            ->findBy([], [
                'grade' => 'asc'
            ]);
    }

    /**
     * @inheritDoc
     */
    public function persist(ActiveDirectoryGradeSyncOption $option) {
        $this->em->persist($option);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    public function remove(ActiveDirectoryGradeSyncOption $option) {
        $this->em->remove($option);
        $this->em->flush();
    }
}