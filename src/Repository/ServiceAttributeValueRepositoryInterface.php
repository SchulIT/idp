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
    public function persist($attributeValue);

    /**
     * @param ServiceAttributeValueInterface
     * @return mixed
     */
    public function remove($attributeValue);

    /**
     * @param User $user
     * @return ServiceAttributeValue[]
     */
    public function getAttributeValuesForUser(User $user);

    /**
     * @param UserType $userType
     * @return ServiceAttributeUserTypeValue[]
     */
    public function getAttributeValuesForUserType(UserType $userType);

    /**
     * @param UserRole $userRole
     * @return ServiceAttributeUserRoleValue[]
     */
    public function getAttributeValuesForUserRole(UserRole $userRole);

    /**
     * @param RegistrationCode $code
     * @return ServiceAttributeRegistrationCodeValue[]
     */
    public function getAttributeValuesForRegistrationCode(RegistrationCode $code);

}