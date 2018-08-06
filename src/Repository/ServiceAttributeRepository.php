<?php

namespace App\Repository;

use App\Entity\ServiceAttribute;
use Doctrine\ORM\EntityManagerInterface;

class ServiceAttributeRepository implements ServiceAttributeRepositoryInterface {

    private $_em;

    public function __construct(EntityManagerInterface $objectManager) {
        $this->_em = $objectManager;
    }

    /**
     * @inheritDoc
     */
    public function getAttributes() {
        return $this->_em->getRepository(ServiceAttribute::class)
            ->findAll();
    }

    public function getAttributesForServiceProvider($entityId) {
        $queryBuilder = $this->_em
            ->createQueryBuilder()
            ->select(['a', 's'])
            ->from(ServiceAttribute::class, 'a')
            ->leftJoin('a.services', 's')
            ->where('s.entityId = :entityId')
            ->setParameter('entityId', $entityId);

        return $queryBuilder->getQuery()->getResult();
    }
}