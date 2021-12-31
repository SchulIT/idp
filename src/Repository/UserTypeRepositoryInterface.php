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
    public function findAll(): array;

    /**
     * @return string[]
     */
    public function findAllUuids(): array;

    public function persist(UserType $userType): void;

    public function remove(UserType $userType): void;
}