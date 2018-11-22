<?php

namespace App\Repository;

use App\Entity\UserRole;

interface UserRoleRepositoryInterface {

    /**
     * @return UserRole[]
     */
    public function findAll();

    public function persist(UserRole $role);

    public function remove(UserRole $role);
}