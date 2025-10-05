<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ActiveDirectoryGradeSyncOption;
use Doctrine\ORM\EntityManagerInterface;

class ActiveDirectoryGradeSyncOptionRepository implements ActiveDirectoryGradeSyncOptionRepositoryInterface {

    public function __construct(private readonly EntityManagerInterface $em)
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
    public function persist(ActiveDirectoryGradeSyncOption $option): void {
        $this->em->persist($option);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    public function remove(ActiveDirectoryGradeSyncOption $option): void {
        $this->em->remove($option);
        $this->em->flush();
    }
}
