<?php

namespace App\Repository;

use App\Entity\ActiveDirectoryRoleSyncOption;

interface ActiveDirectoryRoleSyncOptionRepositoryInterface {
    /**
     * @return ActiveDirectoryRoleSyncOption[]
     */
    public function findAll();

    /**
     * @param ActiveDirectoryRoleSyncOption $option
     */
    public function persist(ActiveDirectoryRoleSyncOption $option);

    /**
     * @param ActiveDirectoryRoleSyncOption $option
     */
    public function remove(ActiveDirectoryRoleSyncOption $option);
}