<?php

namespace App\Repository;

use App\Entity\ActiveDirectoryGradeSyncOption;

interface ActiveDirectoryGradeSyncOptionRepositoryInterface {
    /**
     * @return ActiveDirectoryGradeSyncOption[]
     */
    public function findAll(): array;

    public function persist(ActiveDirectoryGradeSyncOption $option);

    public function remove(ActiveDirectoryGradeSyncOption $option);
}