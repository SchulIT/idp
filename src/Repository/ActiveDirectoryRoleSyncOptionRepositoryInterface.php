<?php

namespace App\Repository;

use App\Entity\ActiveDirectoryRoleSyncOption;

interface ActiveDirectoryRoleSyncOptionRepositoryInterface {
    /**
     * @return ActiveDirectoryRoleSyncOption[]
     */
    public function findAll(): array;

    /**
     * @param ActiveDirectoryRoleSyncOption $option
     */
    public function persist(ActiveDirectoryRoleSyncOption $option): void;

    /**
     * @param ActiveDirectoryRoleSyncOption $option
     */
    public function remove(ActiveDirectoryRoleSyncOption $option): void;
}