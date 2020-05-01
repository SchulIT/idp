<?php

namespace App\Repository;

use App\Entity\UserRegistrationCode;
use App\Entity\UserType;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface UserRegistrationCodeRepositoryInterface extends TransactionalRepositoryInterface {
    public function findOneByCode(string $code): ?UserRegistrationCode;

    public function findOneByToken(string $token): ?UserRegistrationCode;

    public function findAll();

    /**
     * @return string[]
     */
    public function findAllUuids(): array;

    public function persist(UserRegistrationCode $code): void;

    public function remove(UserRegistrationCode $code): void;

    public function resetTokens(\DateTime $dateTime): void;

    public function getPaginatedUsers(int $itemsPerPage, int &$page, UserType $type = null): Paginator;
}