<?php

namespace App\Service\Attribute;

use App\Entity\User;
use App\Repository\ServiceAttributeValueRepositoryInterface;

readonly class EffectiveAttributeResolver {

    public function __construct(
        private ServiceAttributeValueRepositoryInterface $attributeValueRepository
    ) { }

    /**
     * @param User|null $user
     * @return ResolvedValue[]
     */
    public function getDetailedAttributeValuesForUser(User|null $user = null): array {
        if(!$user instanceof User) {
            return [ ];
        }

        $values = [ ];

        // USER TYPE
        foreach($this->attributeValueRepository->getAttributeValuesForUserType($user->getType()) as $attributeValue) {
            $name = $attributeValue->getAttribute()->getName();

            if(!array_key_exists($name, $values)) {
                $values[$name] = new AttributeCollection($attributeValue->getAttribute());
            }

            $values[$name]->userTypeValue = $attributeValue;
        }

        // USER ROLES
        foreach($user->getUserRoles() as $userRole) {
            foreach($this->attributeValueRepository->getAttributeValuesForUserRole($userRole) as $attributeValue) {
                $name = $attributeValue->getAttribute()->getName();

                if(!array_key_exists($name, $values)) {
                    $values[$name] = new AttributeCollection($attributeValue->getAttribute());
                }

                $values[$name]->addUserRoleValue($attributeValue);
            }
        }

        // USER
        foreach($this->attributeValueRepository->getAttributeValuesForUser($user) as $attributeValue) {
            $name = $attributeValue->getAttribute()->getName();

            if(!array_key_exists($name, $values)) {
                $values[$name] = new AttributeCollection($attributeValue->getAttribute());
            }

            $values[$name]->userValue = $attributeValue;
        }

        return array_map(
            fn(AttributeCollection $collection) => $collection->attribute->isMultipleChoice() && $collection->attribute->isMergeValues() ? $this->resolveMultiValue($collection) : $this->resolve($collection),
            $values
        );
    }

    private function resolve(AttributeCollection $collection): ResolvedValue {
        $value = $collection->userTypeValue?->getValue();
        $source = $collection->userTypeValue;

        foreach($collection->getUserRoleValues() as $userRoleValue) {
            $value = $userRoleValue->getValue();
            $source = $userRoleValue;
        }

        if($collection->userValue !== null) {
            $value = $collection->userValue->getValue();
            $source = $collection->userValue;
        }

        return new ResolvedValue(
            $collection->attribute,
            $value,
            [ $source ]
        );
    }

    private function resolveMultiValue(AttributeCollection $collection): ResolvedValue {
        $value = [ ];
        $sources = [ ];

        if($collection->userTypeValue !== null && is_array($collection->userTypeValue->getValue())) {
            $value = $collection->userTypeValue->getValue();
            $sources[] = $collection->userTypeValue;
        }

        foreach($collection->getUserRoleValues() as $userRoleValue) {
            if(is_array($userRoleValue->getValue())) {
                $value = array_merge($value, $userRoleValue->getValue());
                $sources[] = $userRoleValue;
            }
        }

        if($collection->userValue !== null && is_array($collection->userValue->getValue())) {
            $value = array_merge($value, $collection->userValue->getValue());
            $sources[] = $collection->userValue;
        }

        return new ResolvedValue(
            $collection->attribute,
            $value,
            $sources
        );
    }
}