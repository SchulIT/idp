<?php

namespace App\Repository;

use App\Entity\ActiveDirectoryRoleSyncOption;
use Doctrine\ORM\EntityManagerInterface;

class ActiveDirectoryRoleSyncOptionRepository implements ActiveDirectoryRoleSyncOptionRepositoryInterface {

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em
            ->getRepository(ActiveDirectoryRoleSyncOption::class)
            ->findBy([], [
                'name' => 'asc'
            ]);
    }

    /**
     * @inheritDoc
     */
    public function persist(ActiveDirectoryRoleSyncOption $option): void {
        $this->em->persist($option);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    public function remove(ActiveDirectoryRoleSyncOption $option): void {
        $this->em->remove($option);
        $this->em->flush();
    }
}