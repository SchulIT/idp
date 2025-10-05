<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ActiveDirectorySyncOption;

interface ActiveDirectorySyncOptionRepositoryInterface {

    /**
     * @return ActiveDirectorySyncOption[]
     */
    public function findAll(): array;

    public function persist(ActiveDirectorySyncOption $option): void;

    public function remove(ActiveDirectorySyncOption $option): void;
}
