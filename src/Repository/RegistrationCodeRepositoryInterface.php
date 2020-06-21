<?php

namespace App\Repository;

use App\Entity\RegistrationCode;
use App\Entity\UserType;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface RegistrationCodeRepositoryInterface extends TransactionalRepositoryInterface {
    public function findOneByCode(string $code): ?RegistrationCode;

    public function findOneByToken(string $token): ?RegistrationCode;

    public function findAll();

    /**
     * @return string[]
     */
    public function findAllUuids(): array;

    public function persist(RegistrationCode $code): void;

    public function remove(RegistrationCode $code): void;

    public function resetTokens(\DateTime $dateTime): void;

    public function removeRedeemed(): void;

    public function getPaginatedUsers(int $itemsPerPage, int &$page, UserType $type = null): Paginator;
}