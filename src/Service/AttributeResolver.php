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

    private $attributeValueRepository;

    public function __construct(ServiceAttributeValueRepositoryInterface $attributeValueRepository) {
        $this->attributeValueRepository = $attributeValueRepository;
    }

    /**
     * @param User|null $user
     * @return ServiceAttributeValueInterface[] $user
     */
    public function getDetailedResultingAttributeValuesForUser(User $user = null) {
        if($user === null) {
            return [ ];
        }

        $values = [ ];

        $keyFunc = function(ServiceAttributeValueInterface $attributeValue) {
            return $attributeValue->getAttribute()->getId();
        };

        // USER TYPE
        $typeValues = $this->attributeValueRepository->getAttributeValuesForUserType($user->getType());
        $typeValues = $this->makeArrayWithKeys($typeValues, $keyFunc);
        $values = array_merge($values, $typeValues);

        // USER ROLES
        /** @var UserRole[] $userRoles */
        $userRoles = $user->getUserRoles()->toArray();
        usort($userRoles, function(UserRole $roleA, UserRole $roleB) {
            return $roleB->getPriority() - $roleA->getPriority();
        });

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
    public function getResultingAttributeValuesForUser(User $user = null) {
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
    public function getAttributeValuesForUser(User $user = null) {
        if($user === null) {
            return [ ];
        }

        /** @var ServiceAttributeValue[] $userAttributeValues */
        $userAttributeValues = $this->attributeValueRepository->getAttributeValuesForUser($user);

        return $this->transformValuesToSimpleArray($userAttributeValues);
    }

    /**
     * @param UserType $userType
     * @return mixed[]
     */
    public function getAttributesForType(UserType $userType) {
        /** @var ServiceAttributeValue[] $userTypeAttributeValues */
        $userTypeAttributeValues = $this->attributeValueRepository->getAttributeValuesForUserType($userType);

        return $this->transformValuesToSimpleArray($userTypeAttributeValues);
    }

    /**
     * @param UserRole $userRole
     * @return mixed[]
     */
    public function getAttributesForRole(UserRole $userRole) {
        /** @var ServiceAttributeValue[] $userRoleAttributeValues */
        $userRoleAttributeValues = $this->attributeValueRepository->getAttributeValuesForUserRole($userRole);

        return $this->transformValuesToSimpleArray($userRoleAttributeValues);
    }

    /**
     * @param RegistrationCode $code
     * @return mixed[]
     */
    public function getAttributesForRegistrationCode(RegistrationCode $code) {
        /** @var ServiceAttributeValue[] $registrationCodeValues */
        $registrationCodeValues = $this->attributeValueRepository->getAttributeValuesForRegistrationCode($code);

        return $this->transformValuesToSimpleArray($registrationCodeValues);
    }

    private function transformValuesToSimpleArray(array $values) {
        $attributeValues = [ ];

        foreach($values as $attribute) {
            $attributeValues[$attribute->getAttribute()->getName()] = $attribute->getValue();
        }

        return $attributeValues;
    }
}