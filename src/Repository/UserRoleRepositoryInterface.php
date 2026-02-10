<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserRole;

interface UserRoleRepositoryInterface {

    public function findOneByUuid(string $uuid): ?UserRole;

    /**
     * @return UserRole[]
     */
    public function findAll(): array;

    public function persist(UserRole $role): void;

    public function remove(UserRole $role): void;
}
