<?php

namespace App\Service;

use App\Entity\ServiceAttribute;
use App\Entity\ServiceAttributeRegistrationCodeValue;
use App\Entity\ServiceAttributeUserRoleValue;
use App\Entity\ServiceAttributeUserTypeValue;
use App\Entity\ServiceAttributeValue;
use App\Entity\User;
use App\Entity\RegistrationCode;
use App\Entity\UserRole;
use App\Entity\UserType;
use App\Repository\ServiceAttributeRepositoryInterface;
use App\Repository\ServiceAttributeValueRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Traits\ArrayTrait;

/**
 * Helper which persists attributes of a user in the database.
 */
class AttributePersister {
    use ArrayTrait;

    private $attributeRepository;
    private $attributeValueRepository;

    public function __construct(ServiceAttributeRepositoryInterface $attributeRepository, ServiceAttributeValueRepositoryInterface $attributeValueRepository) {
        $this->attributeRepository = $attributeRepository;
        $this->attributeValueRepository = $attributeValueRepository;
    }

    public function persist(array $values, array $currentAttributes, \Closure $factory) {
        /** @var ServiceAttribute[] $attributes */
        $attributes = $this->makeArrayWithKeys(
            $this->attributeRepository->findAll(),
            function(ServiceAttribute $attribute) {
                return $attribute->getName();
            }
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

    public function persistUserAttributes(array $values, User $user) {
        $factory = function() use ($user) {
            $value = (new ServiceAttributeValue())
                ->setUser($user);
            $user->getAttributes()->add($value);

            return $value;
        };

        /** @var ServiceAttributeValue[] $currentUserAttributes */
        $currentUserAttributes = $this->makeArrayWithKeys(
            $user->getAttributes()->toArray(),
            function(ServiceAttributeValue $attributeValue) {
                return $attributeValue->getAttribute()->getName();
            }
        );

        $this->persist($values, $currentUserAttributes, $factory);
    }

    public function persistUserRoleAttributes(array $values, UserRole $userRole) {
        $factory = function() use ($userRole) {
            $value = (new ServiceAttributeUserRoleValue())
                ->setUserRole($userRole);
            $userRole->getAttributes()->add($value);

            return $value;
        };

        /** @var ServiceAttributeUserRoleValue[] $currentUserAttributes */
        $currentUserAttributes = $this->makeArrayWithKeys(
            $userRole->getAttributes()->toArray(),
            function(ServiceAttributeUserRoleValue $attributeValue) {
                return $attributeValue->getAttribute()->getName();
            }
        );

        $this->persist($values, $currentUserAttributes, $factory);
    }

    public function persistUserTypeAttributes(array $values, UserType $userType) {
        $factory = function() use ($userType) {
            $value = (new ServiceAttributeUserTypeValue())
                ->setUserType($userType);
            $userType->getAttributes()->add($value);

            return $value;
        };

        /** @var ServiceAttributeUserRoleValue[] $currentUserAttributes */
        $currentUserAttributes = $this->makeArrayWithKeys(
            $userType->getAttributes()->toArray(),
            function(ServiceAttributeUserTypeValue $attributeValue) {
                return $attributeValue->getAttribute()->getName();
            }
        );

        $this->persist($values, $currentUserAttributes, $factory);
    }

    public function persistRegistrationCodeAttributes(array $values, RegistrationCode $code) {
        $factory = function() use ($code) {
            $value = (new ServiceAttributeRegistrationCodeValue())
                ->setRegistrationCode($code);
            $code->getAttributes()->add($value);

            return $value;
        };

        $currentRegistrationCodeAttributes = $this->makeArrayWithKeys(
            $code->getAttributes()->toArray(),
            function(ServiceAttributeRegistrationCodeValue $attributeValue) {
                return $attributeValue->getAttribute()->getName();
            }
        );

        $this->persist($values, $currentRegistrationCodeAttributes, $factory);
    }
}