<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SamlServiceProvider;
use App\Entity\ServiceProvider;
use Doctrine\ORM\EntityManagerInterface;

class ServiceProviderRepository implements ServiceProviderRepositoryInterface {

    public function __construct(private readonly EntityManagerInterface $em)
    {
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
