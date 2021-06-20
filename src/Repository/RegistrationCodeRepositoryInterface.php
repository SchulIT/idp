<?php

namespace App\Repository;

use App\Entity\RegistrationCode;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface RegistrationCodeRepositoryInterface extends TransactionalRepositoryInterface {
    public function findOneByCode(string $code): ?RegistrationCode;

    public function findAll();

    /**
     * @return string[]
     */
    public function findAllUuids(): array;

    /**
     * @param string $grade
     * @return RegistrationCode[]
     */
    public function findByGrade(string $grade): array;

    public function persist(RegistrationCode $code): void;

    public function remove(RegistrationCode $code): void;

    public function getPaginatedUsers(int $itemsPerPage, int &$page, ?string $query = null, ?string $grade = null): Paginator;

    public function removeRedeemed(): void;
}