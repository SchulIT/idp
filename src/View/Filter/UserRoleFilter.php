<?php

namespace App\View\Filter;

use App\Entity\UserRole;
use App\Repository\UserRoleRepositoryInterface;
use App\Utils\ArrayUtils;

class UserRoleFilter {
    private $userRoleRepository;

    public function __construct(UserRoleRepositoryInterface $repository) {
        $this->userRoleRepository = $repository;
    }

    public function handle(?string $roleUuid): UserRoleFilterView {
        $roles = ArrayUtils::createArrayWithKeys(
            $this->userRoleRepository->findAll(),
            function(UserRole $role) {
                return (string)$role->getUuid();
            });

        $currentRole = $roles[$roleUuid] ?? null;

        return new UserRoleFilterView($roles, $currentRole);
    }
}