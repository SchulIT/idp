<?php

declare(strict_types=1);

namespace App\View\Filter;

use App\Entity\UserRole;

class UserRoleFilterView {

    /**
     * @param UserRole[] $roles
     */
    public function __construct(private readonly array $roles, private readonly ?UserRole $currentRole)
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
