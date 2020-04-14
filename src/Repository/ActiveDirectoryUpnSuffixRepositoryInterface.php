<?php

namespace App\Repository;

use App\Entity\ActiveDirectoryUpnSuffix;

interface  ActiveDirectoryUpnSuffixRepositoryInterface {

    /**
     * @return ActiveDirectoryUpnSuffix[]
     */
    public function findAll(): array;

    /**
     * @param ActiveDirectoryUpnSuffix $suffix
     */
    public function persist(ActiveDirectoryUpnSuffix $suffix): void;

    /**
     * @param ActiveDirectoryUpnSuffix $suffix
     */
    public function remove(ActiveDirectoryUpnSuffix $suffix): void;
}