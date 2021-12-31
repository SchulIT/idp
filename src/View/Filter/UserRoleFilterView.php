<?php

namespace App\View\Filter;

use App\Entity\UserRole;

class UserRoleFilterView {

    /** @var UserRole[] */
    private array $roles;

    /** @var UserRole|null */
    private ?UserRole $currentRole;

    public function __construct(array $roles, ?UserRole $currentRole) {
        $this->roles = $roles;
        $this->currentRole = $currentRole;
    }

    /**
     * @return UserRole[]
     */
    public function getRoles(): array {
        return $this->roles;
    }

    /**
     * @return UserRole|null
     */
    public function getCurrentRole(): ?UserRole {
        return $this->currentRole;
    }
}