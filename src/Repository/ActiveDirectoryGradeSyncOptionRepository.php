<?php

namespace App\Repository;

use App\Entity\ActiveDirectoryGradeSyncOption;
use Doctrine\ORM\EntityManagerInterface;

class ActiveDirectoryGradeSyncOptionRepository implements ActiveDirectoryGradeSyncOptionRepositoryInterface {

    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array {
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