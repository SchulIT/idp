<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Setting;

interface SettingRepositoryInterface {

    public function findOneByKey(string $key): ?Setting;

    /**
     * @return Setting[]
     */
    public function findAll(): array;

    public function persist(Setting $setting): void;
}
