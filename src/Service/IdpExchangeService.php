<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use App\Saml\AttributeValueProvider;
use SchoolIT\IdpExchange\Request\UpdatedUsersRequest;
use SchoolIT\IdpExchange\Request\UserRequest;
use SchoolIT\IdpExchange\Request\UsersRequest;
use SchoolIT\IdpExchange\Response\Builder\UpdatedUsersResponseBuilder;
use SchoolIT\IdpExchange\Response\Builder\UserResponseBuilder;
use SchoolIT\IdpExchange\Response\Builder\UsersResponseBuilder;
use SchoolIT\IdpExchange\Response\UpdatedUsersResponse;
use SchoolIT\CommonBundle\Saml\ClaimTypes as ExtendedClaimTypes;

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

    public function getUsers(UsersRequest $request) {
        $users = $this->userRepository->findUsersByUsernames($request->usernames);

        $builder = new UsersResponseBuilder();

        foreach($users as $user) {
            $builder->addUser($this->buildUserResponse($user));
        }

        return $builder->build();
    }

    public function getUser(UserRequest $request) {
        $user = $this->userRepository->findOneByUsername($request->username);

        $builder = new UserResponseBuilder();

        if($user !== null) {
            $builder
                ->setUsername($user->getUsername());
        }

        return $builder->build();
    }

    private function buildUserResponse(User $user = null) {
        $builder = new UserResponseBuilder();

        if($user !== null) {
            $builder->setUsername($user->getUsername());

            $attributes = $this->attributeValueProvider->getValuesForUser($user, null);
            foreach($attributes as $attribute) {
                if($attribute->getName() === ExtendedClaimTypes::SERVICES) {
                    /**
                     * Do not expose list of services through ExchangeAPI
                     */
                    continue;
                }

                $values = $attribute->getAllAttributeValues();

                if($values !== null && is_array($values)) {
                    if (count($values) > 1) {
                        $builder
                            ->addValuesAttribute($attribute->getName(), $values);
                    } else {
                        $builder
                            ->addValueAttribute($attribute->getName(), $values[0]);
                    }
                } else {
                    $builder
                        ->addValueAttribute($attribute->getName(), null);
                }
            }
        }

        return $builder->build();
    }

    private function computeLastUpdate(User $user) {
        $max = $user->getUpdatedAt();

        foreach($user->getAttributes() as $attribute) {
            $max = max($max, $attribute->getUpdatedAt());
        }

        return $max;
    }
}