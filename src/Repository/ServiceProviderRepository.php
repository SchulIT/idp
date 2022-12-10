<?php

namespace App\Repository;

use App\Entity\SamlServiceProvider;
use App\Entity\ServiceProvider;
use Doctrine\ORM\EntityManagerInterface;

class ServiceProviderRepository implements ServiceProviderRepositoryInterface {

    public function __construct(private EntityManagerInterface $em)
    {
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
    public function findAll(): array {
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

    public function persist(ServiceProvider $provider): void {
        $this->em->persist($provider);
        $this->em->flush();
    }

    public function remove(ServiceProvider $provider): void {
        $this->em->remove($provider);
        $this->em->flush();
    }
}