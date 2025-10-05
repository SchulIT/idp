<?php

declare(strict_types=1);

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
    public function persist(ServiceAttributeValueInterface $attributeValue): void;

    public function remove(ServiceAttributeValueInterface $attributeValue): void;

    /**
     * @return ServiceAttributeValue[]
     */
    public function getAttributeValuesForUser(User $user): array;

    /**
     * @return ServiceAttributeUserTypeValue[]
     */
    public function getAttributeValuesForUserType(UserType $userType): array;

    /**
     * @return ServiceAttributeUserRoleValue[]
     */
    public function getAttributeValuesForUserRole(UserRole $userRole): array;

}
