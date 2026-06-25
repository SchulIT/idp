<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ServiceAttributeValue;
use App\Entity\User;
use App\Entity\UserRole;
use App\Entity\UserType;
use App\Repository\ServiceAttributeValueRepositoryInterface;
use App\Traits\ArrayTrait;

readonly class AttributeResolver {
    use ArrayTrait;

    public function __construct(
        private ServiceAttributeValueRepositoryInterface $attributeValueRepository
    ) { }

    public function getAttributeValuesForUser(User|null $user = null): array {
        if(!$user instanceof User) {
            return [ ];
        }

        $userAttributeValues = $this->attributeValueRepository->getAttributeValuesForUser($user);

        return $this->transformValuesToSimpleArray($userAttributeValues);
    }

    public function getAttributesForType(UserType $userType): array {
        /** @var ServiceAttributeValue[] $userTypeAttributeValues */
        $userTypeAttributeValues = $this->attributeValueRepository->getAttributeValuesForUserType($userType);

        return $this->transformValuesToSimpleArray($userTypeAttributeValues);
    }

    public function getAttributesForRole(UserRole $userRole): array {
        /** @var ServiceAttributeValue[] $userRoleAttributeValues */
        $userRoleAttributeValues = $this->attributeValueRepository->getAttributeValuesForUserRole($userRole);

        return $this->transformValuesToSimpleArray($userRoleAttributeValues);
    }

    private function transformValuesToSimpleArray(array $values): array {
        $attributeValues = [ ];

        foreach($values as $attribute) {
            $attributeValues[$attribute->getAttribute()->getName()] = $attribute->getValue();
        }

        return $attributeValues;
    }
}
