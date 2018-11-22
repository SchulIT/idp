<?php

namespace App\Repository;

use App\Entity\ServiceAttribute;
use Doctrine\ORM\EntityManagerInterface;

class ServiceAttributeRepository implements ServiceAttributeRepositoryInterface {

    private $em;

    public function __construct(EntityManagerInterface $objectManager) {
        $this->em = $objectManager;
    }

    /**
     * @inheritDoc
     */
    public function getAttributes() {
        return $this->findAll();
    }

    public function getAttributesForServiceProvider($entityId) {
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
    public function findAll() {
        return $this->em
            ->getRepository(ServiceAttribute::class)
            ->findAll();
    }

    public function persist(ServiceAttribute $attribute) {
        $this->em->persist($attribute);
        $this->em->flush();
    }

    public function remove(ServiceAttribute $attribute) {
        $this->em->remove($attribute);
        $this->em->flush();
    }
}