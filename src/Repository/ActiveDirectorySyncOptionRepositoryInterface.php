<?php

namespace App\Repository;

use App\Entity\ActiveDirectorySyncOption;

interface ActiveDirectorySyncOptionRepositoryInterface {

    /**
     * @return ActiveDirectorySyncOption[]
     */
    public function findAll(): array;

    /**
     * @param ActiveDirectorySyncOption $option
     */
    public function persist(ActiveDirectorySyncOption $option): void;

    /**
     * @param ActiveDirectorySyncOption $option
     */
    public function remove(ActiveDirectorySyncOption $option): void;
}