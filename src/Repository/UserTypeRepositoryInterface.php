<?php

namespace App\Repository;

use App\Entity\UserType;

interface UserTypeRepositoryInterface {
    public function countUsersOfUserType(UserType $userType): int;

    /**
     * @return UserType[]
     */
    public function findAll();
}