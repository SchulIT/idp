<?php

namespace App\Repository;

use App\Entity\User;

interface UserRepositoryInterface {
    public function getUsersUpdatedAfter(\DateTime $dateTime);

    public function findOneByUsername(string $username): ?User;

    public function findAll($offset = 0, $limit = null);
}