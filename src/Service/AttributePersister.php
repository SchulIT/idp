<?php

namespace App\Service;

use App\Entity\ServiceAttribute;
use App\Entity\ServiceAttributeUserRoleValue;
use App\Entity\ServiceAttributeUserTypeValue;
use App\Entity\ServiceAttributeValue;
use App\Entity\User;
use App\Entity\UserRole;
use App\Entity\UserType;
use App\Repository\ServiceAttributeRepositoryInterface;
use App\Repository\ServiceAttributeValueRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Traits\ArrayTrait;
use Closure;

/**
 * Helper which persists attributes of a user in the database.
 */
class AttributePersister {
    use ArrayTrait;

    public function __construct(private ServiceAttributeRepositoryInterface $attributeRepository, private ServiceAttributeValueRepositoryInterface $attributeValueRepository)
    {
    }

    public function persist(array $values, array $currentAttributes, Closure $factory): void {
        /** @var ServiceAttribute[] $attributes */
        $attributes = $this->makeArrayWithKeys(
            $this->attributeRepository->findAll(),
            fn(ServiceAttribute $attribute) => $attribute->getName()
        );

        if($this->attributeValueRepository instanceof TransactionalRepositoryInterface) {
            $this->attributeValueRepository->beginTransaction();
        }

        foreach($values as $name => $value) {
            if(!array_key_exists($name, $attributes)) {
                continue; // TODO: maybe throw Exception here?!
            }

            $currentAttributeValue = null;

            if (array_key_exists($name, $currentAttributes)) {
                $currentAttributeValue = $currentAttributes[$name];
            }

            if($currentAttributeValue !== null && ($value === null || empty($value))) {
                $this->attributeValueRepository->remove($currentAttributeValue);
            } elseif ($value !== null && !empty($value)) {
                if($currentAttributeValue === null) {
                    $currentAttributeValue = $factory();
                    $currentAttributeValue->setAttribute($attributes[$name]);
                }

                $currentAttributeValue->setValue($value);
                $this->attributeValueRepository->persist($currentAttributeValue);
            }
        }

        if($this->attributeValueRepository instanceof TransactionalRepositoryInterface) {
            $this->attributeValueRepository->commit();
        }
    }

    public function persistUserAttributes(array $values, User $user): void {
        $factory = function() use ($user) {
            $value = (new ServiceAttributeValue())
                ->setUser($user);
            $user->getAttributes()->add($value);

            return $value;
        };

        /** @var ServiceAttributeValue[] $currentUserAttributes */
        $currentUserAttributes = $this->makeArrayWithKeys(
            $user->getAttributes()->toArray(),
            fn(ServiceAttributeValue $attributeValue) => $attributeValue->getAttribute()->getName()
        );

        $this->persist($values, $currentUserAttributes, $factory);
    }

    public function persistUserRoleAttributes(array $values, UserRole $userRole): void {
        $factory = function() use ($userRole) {
            $value = (new ServiceAttributeUserRoleValue())
                ->setUserRole($userRole);
            $userRole->getAttributes()->add($value);

            return $value;
        };

        /** @var ServiceAttributeUserRoleValue[] $currentUserAttributes */
        $currentUserAttributes = $this->makeArrayWithKeys(
            $userRole->getAttributes()->toArray(),
            fn(ServiceAttributeUserRoleValue $attributeValue) => $attributeValue->getAttribute()->getName()
        );

        $this->persist($values, $currentUserAttributes, $factory);
    }

    public function persistUserTypeAttributes(array $values, UserType $userType): void {
        $factory = function() use ($userType) {
            $value = (new ServiceAttributeUserTypeValue())
                ->setUserType($userType);
            $userType->getAttributes()->add($value);

            return $value;
        };

        /** @var ServiceAttributeUserRoleValue[] $currentUserAttributes */
        $currentUserAttributes = $this->makeArrayWithKeys(
            $userType->getAttributes()->toArray(),
            fn(ServiceAttributeUserTypeValue $attributeValue) => $attributeValue->getAttribute()->getName()
        );

        $this->persist($values, $currentUserAttributes, $factory);
    }
}