<?php

namespace App\Repository;

use App\Entity\User;

interface UserRepositoryInterface {
    /**
     * @param string[] $usernames
     * @return User[]
     */
    public function findUsersByUsernames(array $usernames);

    /**
     * @param \DateTime $dateTime
     * @param string[] $usernames
     * @return User[]
     */
    public function findUsersUpdatedAfter(\DateTime $dateTime, array $usernames = [ ]);

    public function findOneByUsername(string $username): ?User;

    public function findAll($offset = 0, $limit = null);
}