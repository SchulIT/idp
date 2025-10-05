<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ActiveDirectorySyncOption;
use Doctrine\ORM\EntityManagerInterface;

class ActiveDirectorySyncOptionRepository implements ActiveDirectorySyncOptionRepositoryInterface {

    public function __construct(private readonly EntityManagerInterface $em)
    {
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
