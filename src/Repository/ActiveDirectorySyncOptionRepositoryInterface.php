<?php

namespace App\Repository;

use App\Entity\ActiveDirectorySyncOption;

interface ActiveDirectorySyncOptionRepositoryInterface {

    /**
     * @return ActiveDirectorySyncOption[]
     */
    public function findAll();

    /**
     * @param ActiveDirectorySyncOption $option
     */
    public function persist(ActiveDirectorySyncOption $option);

    /**
     * @param ActiveDirectorySyncOption $option
     */
    public function remove(ActiveDirectorySyncOption $option);
}