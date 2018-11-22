<?php

namespace App\Repository;

use App\Entity\Application;
use Doctrine\ORM\EntityManagerInterface;

class ApplicationRepository implements ApplicationRepositoryInterface {

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function findAll() {
        return $this->em
            ->getRepository(Application::class)
            ->findBy([], [
                'name' => 'asc'
            ]);
    }

    public function persist(Application $application) {
        $this->em->persist($application);
        $this->em->flush();
    }

    public function remove(Application $application) {
        $this->em->remove($application);
        $this->em->flush();
    }

    public function findOneByApiKey($key): ?Application {
        return $this->em
            ->getRepository(Application::class)
            ->findOneBy(['apiKey' => $key]);
    }
}