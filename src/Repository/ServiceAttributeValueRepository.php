<?php

namespace App\Repository;

use App\Entity\ServiceAttributeRegistrationCodeValue;
use App\Entity\ServiceAttributeUserRoleValue;
use App\Entity\ServiceAttributeUserTypeValue;
use App\Entity\ServiceAttributeValue;
use App\Entity\User;
use App\Entity\RegistrationCode;
use App\Entity\UserRole;
use App\Entity\UserType;
use Doctrine\ORM\EntityManagerInterface;

class ServiceAttributeValueRepository implements ServiceAttributeValueRepositoryInterface, TransactionalRepositoryInterface {

    private $em;

    public function __construct(EntityManagerInterface $objectManager) {
        $this->em = $objectManager;
    }

    /**
     * @inheritDoc
     */
    public function persist($attributeValue) {
        $this->em->persist($attributeValue);
    }

    /**
     * @inheritDoc
     */
    public function remove($attributeValue) {
        $this->em->remove($attributeValue);
    }

    public function beginTransaction() {
        $this->em->beginTransaction();
    }

    public function commit() {
        $this->em->commit();
        $this->em->flush();
    }

    public function rollBack() {
        $this->em->rollback();
    }

    /**
     * @inheritDoc
     */
    public function getAttributeValuesForUser(User $user) {
        $query = $this->em
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
        $query = $this->em
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
        $query = $this->em
            ->createQueryBuilder()
            ->select(['v', 'a'])
            ->from(ServiceAttributeUserRoleValue::class, 'v')
            ->leftJoin('v.attribute', 'a')
            ->where('v.userRole = :role')
            ->setParameter('role', $userRole->getId());

        return $query->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function getAttributeValuesForRegistrationCode(RegistrationCode $code) {
        $query = $this->em
            ->createQueryBuilder()
            ->select(['v', 'a'])
            ->from(ServiceAttributeRegistrationCodeValue::class, 'v')
            ->leftJoin('v.attribute', 'a')
            ->where('v.registrationCode = :code')
            ->setParameter('code', $code->getId());

        return $query->getQuery()->getResult();
    }
}