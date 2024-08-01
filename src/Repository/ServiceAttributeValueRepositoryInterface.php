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

interface ServiceAttributeValueRepositoryInterface {
    /**
     * @param ServiceAttributeValueInterface $attributeValue
     */
    public function persist(ServiceAttributeValueInterface $attributeValue): void;

    /**
     * @param ServiceAttributeValueInterface $attributeValue
     */
    public function remove(ServiceAttributeValueInterface $attributeValue): void;

    /**
     * @param User $user
     * @return ServiceAttributeValue[]
     */
    public function getAttributeValuesForUser(User $user): array;

    /**
     * @param UserType $userType
     * @return ServiceAttributeUserTypeValue[]
     */
    public function getAttributeValuesForUserType(UserType $userType): array;

    /**
     * @param UserRole $userRole
     * @return ServiceAttributeUserRoleValue[]
     */
    public function getAttributeValuesForUserRole(UserRole $userRole): array;

}