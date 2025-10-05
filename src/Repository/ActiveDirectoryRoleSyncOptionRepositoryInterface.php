<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ActiveDirectoryRoleSyncOption;

interface ActiveDirectoryRoleSyncOptionRepositoryInterface {
    /**
     * @return ActiveDirectoryRoleSyncOption[]
     */
    public function findAll(): array;

    public function persist(ActiveDirectoryRoleSyncOption $option): void;

    public function remove(ActiveDirectoryRoleSyncOption $option): void;
}
