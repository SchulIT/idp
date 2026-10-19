<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SamlServiceProvider;
use App\Entity\ServiceAttribute;
use App\Entity\ServiceProvider;
use Doctrine\ORM\EntityManagerInterface;

class ServiceAttributeRepository implements ServiceAttributeRepositoryInterface {

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @inheritDoc
     */
    public function getAttributes(): array {
        return $this->findAll();
    }

    public function getAttributesForSamlServiceProvider($entityId): array {
        $qbInner = $this->em->createQueryBuilder()
            ->select('sInner.id')
            ->from(SamlServiceProvider::class, 'sInner')
            ->where('sInner.entityId = :entityId');

        $queryBuilder = $this->em
            ->createQueryBuilder()
            ->select(['a', 's'])
            ->from(ServiceAttribute::class, 'a')
            ->leftJoin('a.services', 's');

        $queryBuilder
            ->where(
                $queryBuilder->expr()->in('s.id', $qbInner->getDQL())
            )
            ->setParameter('entityId', $entityId);

        return $queryBuilder->getQuery()->getResult();
    }

    public function getAttributesForServiceProvider(ServiceProvider $serviceProvider): array {
        $queryBuilder = $this->em
            ->createQueryBuilder()
            ->select(['a', 's'])
            ->from(ServiceAttribute::class, 'a')
            ->leftJoin('a.services', 's')
            ->where('s.id = :serviceProvider')
            ->setParameter('serviceProvider', $serviceProvider->getId());

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em
            ->getRepository(ServiceAttribute::class)
            ->findAll();
    }

    public function persist(ServiceAttribute $attribute): void {
        $this->em->persist($attribute);
        $this->em->flush();
    }

    public function remove(ServiceAttribute $attribute): void {
        $this->em->remove($attribute);
        $this->em->flush();
    }
}
