<?php

namespace App\Repository;

use App\Entity\KioskUser;

interface KioskUserRepositoryInterface {

    public function findOneByToken(string $token): ?KioskUser;

    public function findAll(): array;

    public function persist(KioskUser $user): void;

    public function remove(KioskUser $user): void;
}