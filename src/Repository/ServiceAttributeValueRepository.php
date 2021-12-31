<?php

namespace App\Repository;

use App\Entity\ServiceAttributeRegistrationCodeValue;
use App\Entity\ServiceAttributeUserRoleValue;
use App\Entity\ServiceAttributeUserTypeValue;
use App\Entity\ServiceAttributeValue;
use App\Entity\ServiceAttributeValueInterface;
use App\Entity\User;
use App\Entity\RegistrationCode;
use App\Entity\UserRole;
use App\Entity\UserType;
use Doctrine\ORM\EntityManagerInterface;

class ServiceAttributeValueRepository implements ServiceAttributeValueRepositoryInterface, TransactionalRepositoryInterface {

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $objectManager) {
        $this->em = $objectManager;
    }

    /**
     * @inheritDoc
     */
    public function persist(ServiceAttributeValueInterface $attributeValue): void {
        $this->em->persist($attributeValue);
    }

    /**
     * @inheritDoc
     */
    public function remove(ServiceAttributeValueInterface $attributeValue): void {
        $this->em->remove($attributeValue);
    }

    public function beginTransaction(): void {
        $this->em->beginTransaction();
    }

    public function commit(): void {
        $this->em->commit();
        $this->em->flush();
    }

    public function rollBack(): void {
        $this->em->rollback();
    }

    /**
     * @inheritDoc
     */
    public function getAttributeValuesForUser(User $user): array {
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
    public function getAttributeValuesForUserType(UserType $userType): array {
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
    public function getAttributeValuesForUserRole(UserRole $userRole): array {
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
    public function getAttributeValuesForRegistrationCode(RegistrationCode $code): array {
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