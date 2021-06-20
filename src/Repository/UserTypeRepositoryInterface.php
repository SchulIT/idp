<?php

namespace App\Repository;

use App\Entity\UserType;

interface UserTypeRepositoryInterface {
    public function countUsersOfUserType(UserType $userType): int;

    public function findOneByAlias(string $alias): ?UserType;

    public function findOneByUuid(string $uuid): ?UserType;

    /**
     * @return UserType[]
     */
    public function findAll();

    /**
     * @return string[]
     */
    public function findAllUuids();

    public function persist(UserType $userType);

    public function remove(UserType $userType);
}