<?php

namespace App\View\Filter;

use App\Entity\UserRole;

class UserRoleFilterView {

    /**
     * @param UserRole[] $roles
     */
    public function __construct(private array $roles, private ?UserRole $currentRole)
    {
    }

    /**
     * @return UserRole[]
     */
    public function getRoles(): array {
        return $this->roles;
    }

    public function getCurrentRole(): ?UserRole {
        return $this->currentRole;
    }
}