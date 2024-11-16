<?php

namespace App\View\Filter;

use App\Entity\UserRole;
use App\Repository\UserRoleRepositoryInterface;
use App\Utils\ArrayUtils;

class UserRoleFilter {
    public function __construct(private readonly UserRoleRepositoryInterface $userRoleRepository)
    {
    }

    public function handle(?string $roleUuid): UserRoleFilterView {
        $roles = ArrayUtils::createArrayWithKeys(
            $this->userRoleRepository->findAll(),
            fn(UserRole $role) => (string)$role->getUuid());

        $currentRole = $roles[$roleUuid] ?? null;

        return new UserRoleFilterView($roles, $currentRole);
    }
}