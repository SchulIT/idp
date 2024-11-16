<?php

namespace App\Repository;

use App\Entity\Application;
use Doctrine\ORM\EntityManagerInterface;

class ApplicationRepository implements ApplicationRepositoryInterface {

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em
            ->getRepository(Application::class)
            ->findBy([], [
                'name' => 'asc'
            ]);
    }

    public function persist(Application $application): void {
        $this->em->persist($application);
        $this->em->flush();
    }

    public function remove(Application $application): void {
        $this->em->remove($application);
        $this->em->flush();
    }

    public function findOneByApiKey($key): ?Application {
        return $this->em
            ->getRepository(Application::class)
            ->findOneBy(['apiKey' => $key]);
    }
}