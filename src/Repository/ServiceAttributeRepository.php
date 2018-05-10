<?php

namespace App\Repository;

use App\Entity\ServiceAttribute;
use Doctrine\ORM\EntityRepository;

class ServiceAttributeRepository extends EntityRepository implements ServiceAttributeRepositoryInterface {

    /**
     * @inheritDoc
     */
    public function getAttributes() {
        return $this->findAll();
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