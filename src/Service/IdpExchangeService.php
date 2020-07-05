<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use App\Saml\AttributeValueProvider;
use SchulIT\CommonBundle\Saml\ClaimTypes as ExtendedClaimTypes;
use SchulIT\IdpExchange\Request\UpdatedUsersRequest;
use SchulIT\IdpExchange\Request\UserRequest;
use SchulIT\IdpExchange\Request\UsersRequest;
use SchulIT\IdpExchange\Response\Builder\UpdatedUsersResponseBuilder;
use SchulIT\IdpExchange\Response\Builder\UserResponseBuilder;
use SchulIT\IdpExchange\Response\Builder\UsersResponseBuilder;
use SchulIT\IdpExchange\Response\UpdatedUsersResponse;

class IdpExchangeService {
    private $userRepository;
    private $attributeValueProvider;

    public function __construct(UserRepositoryInterface $userRepository, AttributeValueProvider $attributeValueProvider) {
        $this->userRepository = $userRepository;
        $this->attributeValueProvider = $attributeValueProvider;
    }

    /**
     * @param UpdatedUsersRequest $request
     * @return UpdatedUsersResponse
     */
    public function getUpdatedUsers(UpdatedUsersRequest $request) {
        $users = $this->userRepository->findUsersUpdatedAfter($request->since, $request->usernames);

        $builder = new UpdatedUsersResponseBuilder();

        foreach($users as $user) {
            $builder->addUser($user->getUsername(), $this->computeLastUpdate($user));
        }

        return $builder->build();
    }

    public function getUsers(UsersRequest $request, string $entityId) {
        $users = $this->userRepository->findUsersByUsernames($request->usernames);

        $builder = new UsersResponseBuilder();

        foreach($users as $user) {
            $builder->addUser($this->buildUserResponse($user, $entityId));
        }

        return $builder->build();
    }

    public function getUser(UserRequest $request, string $entityId) {
        $user = $this->userRepository->findOneByUsername($request->username);

        return $this->buildUserResponse($user, $entityId);
    }

    private function buildUserResponse(User $user = null, string $entityId = null) {
        $builder = new UserResponseBuilder();

        if($user !== null) {
            $builder->setUsername($user->getUsername());

            $attributes = $this->attributeValueProvider->getValuesForUser($user, $entityId);
            foreach($attributes as $attribute) {
                if($attribute->getName() === ExtendedClaimTypes::SERVICES) {
                    /**
                     * Do not expose list of services through ExchangeAPI
                     */
                    continue;
                }

                $values = $attribute->getAllAttributeValues();

                if($values === null) {
                    $builder
                        ->addValueAttribute($attribute->getName(), null);
                } else if (count($values) > 1) {
                    $builder
                        ->addValuesAttribute($attribute->getName(), $values);
                } else {
                    $builder
                        ->addValueAttribute($attribute->getName(), (string)$values[0]);
                }
            }
        }

        return $builder->build();
    }

    private function computeLastUpdate(User $user): \DateTime {
        $max = $user->getUpdatedAt();

        foreach($user->getAttributes() as $attribute) {
            $max = max($max, $attribute->getUpdatedAt());
        }

        if($max === null) {
            return new \DateTime('2019-01-01');
        }

        return $max;
    }
}