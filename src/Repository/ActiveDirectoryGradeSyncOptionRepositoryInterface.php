<?php

namespace App\Repository;

use App\Entity\ActiveDirectoryGradeSyncOption;

interface ActiveDirectoryGradeSyncOptionRepositoryInterface {
    /**
     * @return ActiveDirectoryGradeSyncOption[]
     */
    public function findAll();

    /**
     * @param ActiveDirectoryGradeSyncOption $option
     */
    public function persist(ActiveDirectoryGradeSyncOption $option);

    /**
     * @param ActiveDirectoryGradeSyncOption $option
     */
    public function remove(ActiveDirectoryGradeSyncOption $option);
}