<?php

namespace App\Repository;

use App\Entity\ServiceAttribute;
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

    public function getAttributesForServiceProvider($entityId): array {
        $queryBuilder = $this->em
            ->createQueryBuilder()
            ->select(['a', 's'])
            ->from(ServiceAttribute::class, 'a')
            ->leftJoin('a.services', 's')
            ->where('s.entityId = :entityId')
            ->setParameter('entityId', $entityId);

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