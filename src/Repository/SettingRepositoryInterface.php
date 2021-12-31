<?php

namespace App\Repository;

use App\Entity\Setting;

interface SettingRepositoryInterface {

    /**
     * @param string $key
     * @return Setting|null
     */
    public function findOneByKey(string $key): ?Setting;

    /**
     * @return Setting[]
     */
    public function findAll(): array;

    /**
     * @param Setting $setting
     */
    public function persist(Setting $setting): void;
}