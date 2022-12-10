<?php

namespace App\Service;

use App\Entity\ServiceAttributeValue;
use App\Entity\ServiceAttributeValueInterface;
use App\Entity\User;
use App\Entity\RegistrationCode;
use App\Entity\UserRole;
use App\Entity\UserType;
use App\Repository\ServiceAttributeValueRepositoryInterface;
use App\Traits\ArrayTrait;

/**
 * Helper which computes all attributes for a given user.
 */
class AttributeResolver {
    use ArrayTrait;

    public function __construct(private ServiceAttributeValueRepositoryInterface $attributeValueRepository)
    {
    }

    /**
     * @param User|null $user
     * @return ServiceAttributeValueInterface[] $user
     */
    public function getDetailedResultingAttributeValuesForUser(User $user = null): array {
        if($user === null) {
            return [ ];
        }

        $values = [ ];

        $keyFunc = fn(ServiceAttributeValueInterface $attributeValue) => $attributeValue->getAttribute()->getId();

        // USER TYPE
        $typeValues = $this->attributeValueRepository->getAttributeValuesForUserType($user->getType());
        $typeValues = $this->makeArrayWithKeys($typeValues, $keyFunc);
        $values = array_merge($values, $typeValues);

        // USER ROLES
        /** @var UserRole[] $userRoles */
        $userRoles = $user->getUserRoles()->toArray();
        usort($userRoles, fn(UserRole $roleA, UserRole $roleB) => $roleB->getPriority() - $roleA->getPriority());

        foreach($userRoles as $role) {
            $roleValues = $this->attributeValueRepository->getAttributeValuesForUserRole($role);
            $roleValues = $this->makeArrayWithKeys($roleValues, $keyFunc);

            foreach($roleValues as $key => $value) {
                if(array_key_exists($key, $values) === false) {
                    $values[$key] = $value;
                }
            }
        }

        // USER
        $userValues = $this->attributeValueRepository->getAttributeValuesForUser($user);
        $userValues = $this->makeArrayWithKeys($userValues, $keyFunc);
        $values = array_merge($values, $userValues);

        return $values;
    }

    /**
     * @param User|null $user
     * @return mixed[]
     */
    public function getResultingAttributeValuesForUser(User $user = null): array {
        if($user === null) {
            return [ ];
        }

        $values = $this->getAttributesForType($user->getType());

        $userRoles = $user->getUserRoles();

        foreach($userRoles as $role) {
            $values = array_merge($values, $this->getAttributesForRole($role));
        }

        $values = array_merge($values, $this->getAttributeValuesForUser($user));

        return $values;
    }

    /**
     * @param User|null $user
     * @return mixed[]
     */
    public function getAttributeValuesForUser(User $user = null): array {
        if($user === null) {
            return [ ];
        }

        /** @var ServiceAttributeValue[] $userAttributeValues */
        $userAttributeValues = $this->attributeValueRepository->getAttributeValuesForUser($user);

        return $this->transformValuesToSimpleArray($userAttributeValues);
    }

    /**
     * @return mixed[]
     */
    public function getAttributesForType(UserType $userType): array {
        /** @var ServiceAttributeValue[] $userTypeAttributeValues */
        $userTypeAttributeValues = $this->attributeValueRepository->getAttributeValuesForUserType($userType);

        return $this->transformValuesToSimpleArray($userTypeAttributeValues);
    }

    /**
     * @return mixed[]
     */
    public function getAttributesForRole(UserRole $userRole): array {
        /** @var ServiceAttributeValue[] $userRoleAttributeValues */
        $userRoleAttributeValues = $this->attributeValueRepository->getAttributeValuesForUserRole($userRole);

        return $this->transformValuesToSimpleArray($userRoleAttributeValues);
    }

    /**
     * @return mixed[]
     */
    public function getAttributesForRegistrationCode(RegistrationCode $code): array {
        /** @var ServiceAttributeValue[] $registrationCodeValues */
        $registrationCodeValues = $this->attributeValueRepository->getAttributeValuesForRegistrationCode($code);

        return $this->transformValuesToSimpleArray($registrationCodeValues);
    }

    private function transformValuesToSimpleArray(array $values): array {
        $attributeValues = [ ];

        foreach($values as $attribute) {
            $attributeValues[$attribute->getAttribute()->getName()] = $attribute->getValue();
        }

        return $attributeValues;
    }
}