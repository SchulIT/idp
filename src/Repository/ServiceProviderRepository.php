<?php

namespace App\Repository;

use App\Entity\SamlServiceProvider;
use App\Entity\ServiceProvider;
use Doctrine\ORM\EntityManagerInterface;

class ServiceProviderRepository implements ServiceProviderRepositoryInterface {

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function findOneByToken(string $token): ?ServiceProvider {
        return $this->em->getRepository(ServiceProvider::class)
            ->findOneBy(['token' => $token]);
    }

    /**
     * @inheritDoc
     */
    public function findAll() {
        return $this->em->getRepository(ServiceProvider::class)
            ->findAll();
    }

    /**
     * @inheritDoc
     */
    public function findOneByEntityId(string $entityId): ?SamlServiceProvider {
        return $this->em->getRepository(SamlServiceProvider::class)
            ->findOneBy(['entityId' => $entityId]);
    }

    public function persist(ServiceProvider $provider) {
        $this->em->persist($provider);
        $this->em->flush();
    }

    public function remove(ServiceProvider $provider) {
        $this->em->remove($provider);
        $this->em->flush();
    }
}