<?php

namespace App\Service;

use App\Entity\ServiceAttributeValue;
use App\Entity\ServiceAttributeValueInterface;
use App\Entity\User;
use App\Entity\UserRole;
use App\Entity\UserType;
use App\Repository\ServiceAttributeValueRepositoryInterface;
use App\Traits\ArrayTrait;

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
        $userRoles = $user->getUserRoles();
        foreach($userRoles as $role) {
            $roleValues = $this->attributeValueRepository->getAttributeValuesForUserRole($role);
            $roleValues = $this->makeArrayWithKeys($roleValues, $keyFunc);
            $values = array_merge($values, $roleValues);
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

        $userRoles = $user->getRoles();

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

        $attributeValues = [ ];

        foreach($userAttributeValues as $attribute) {
            $attributeValues[$attribute->getAttribute()->getName()] = $attribute->getValue();
        }

        return $attributeValues;
    }

    /**
     * @param UserType $userType
     * @return mixed[]
     */
    public function getAttributesForType(UserType $userType) {
        if($userType === null) {
            return [ ];
        }

        /** @var ServiceAttributeValue[] $userAttributeValues */
        $userAttributeValues = $this->attributeValueRepository->getAttributeValuesForUserType($userType);

        $attributeValues = [ ];

        foreach($userAttributeValues as $attribute) {
            $attributeValues[$attribute->getAttribute()->getName()] = $attribute->getValue();
        }

        return $attributeValues;
    }

    /**
     * @param UserRole $userRole
     * @return mixed[]
     */
    public function getAttributesForRole(UserRole $userRole) {
        if($userRole === null) {
            return [ ];
        }

        /** @var ServiceAttributeValue[] $userAttributeValues */
        $userAttributeValues = $this->attributeValueRepository->getAttributeValuesForUserRole($userRole);

        $attributeValues = [ ];

        foreach($userAttributeValues as $attribute) {
            $attributeValues[$attribute->getAttribute()->getName()] = $attribute->getValue();
        }

        return $attributeValues;
    }
}