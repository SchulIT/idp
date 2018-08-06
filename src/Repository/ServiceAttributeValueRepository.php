<?php

namespace App\Repository;

use App\Entity\ServiceAttributeUserRoleValue;
use App\Entity\ServiceAttributeUserTypeValue;
use App\Entity\ServiceAttributeValue;
use App\Entity\User;
use App\Entity\UserRole;
use App\Entity\UserType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class ServiceAttributeValueRepository implements ServiceAttributeValueRepositoryInterface, TransactionalRepositoryInterface {

    private $_em;

    public function __construct(EntityManagerInterface $objectManager) {
        $this->_em = $objectManager;
    }

    /**
     * @inheritDoc
     */
    public function persist($attributeValue) {
        $this->_em->persist($attributeValue);
    }

    /**
     * @inheritDoc
     */
    public function remove($attributeValue) {
        $this->_em->remove($attributeValue);
    }

    public function beginTransaction() {
        $this->_em->beginTransaction();
    }

    public function commit() {
        $this->_em->commit();
        $this->_em->flush();
    }

    public function rollBack() {
        $this->_em->rollback();
    }

    /**
     * @inheritDoc
     */
    public function getAttributeValuesForUser(User $user) {
        $query = $this->_em
            ->createQueryBuilder()
            ->select(['v', 'a'])
            ->from(ServiceAttributeValue::class, 'v')
            ->leftJoin('v.attribute', 'a')
            ->where('v.user = :user')
            ->setParameter('user', $user->getId());

        return $query->getQuery()->getResult();
    }


    /**
     * @inheritDoc
     */
    public function getAttributeValuesForUserType(UserType $userType) {
        $query = $this->_em
            ->createQueryBuilder()
            ->select(['v', 'a'])
            ->from(ServiceAttributeUserTypeValue::class, 'v')
            ->leftJoin('v.attribute', 'a')
            ->where('v.userType = :type')
            ->setParameter('type', $userType->getId());

        return $query->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function getAttributeValuesForUserRole(UserRole $userRole) {
        $query = $this->_em
            ->createQueryBuilder()
            ->select(['v', 'a'])
            ->from(ServiceAttributeUserRoleValue::class, 'v')
            ->leftJoin('v.attribute', 'a')
            ->where('v.userRole = :role')
            ->setParameter('role', $userRole->getId());

        return $query->getQuery()->getResult();
    }
}