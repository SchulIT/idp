<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\OidcServiceProvider;
use App\Entity\SamlServiceProvider;
use App\Entity\ServiceProvider;
use Doctrine\ORM\EntityManagerInterface;
use Override;

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

    #[Override]
    public function findOneOidcByClientIdentifier(string $clientIdentifier): ?OidcServiceProvider {
        return $this->em->createQueryBuilder()
            ->select(['sp', 'c'])
            ->from(OidcServiceProvider::class, 'sp')
            ->join('sp.client', 'c')
            ->where('c.identifier = :identifier')
            ->setParameter('identifier', $clientIdentifier)
            ->getQuery()
            ->getOneOrNullResult();
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
