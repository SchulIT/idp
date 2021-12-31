<?php

namespace App\Repository;

use App\Entity\RegistrationCode;
use App\Entity\User;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface RegistrationCodeRepositoryInterface extends TransactionalRepositoryInterface {
    public function findOneByCode(string $code): ?RegistrationCode;

    public function findAll(): array;

    /**
     * @return string[]
     */
    public function findAllUuids(): array;

    /**
     * @param string $grade
     * @return RegistrationCode[]
     */
    public function findByGrade(string $grade): array;

    /**
     * @param User $user
     * @return RegistrationCode[]
     */
    public function findAllByStudent(User $user): array;

    /**
     * Checks whether at least one code is already in the system. It does not care about whether or not the code
     * was redeemed.
     *
     * @param User $user
     * @return bool
     */
    public function codeForStudentExists(User $user): bool;

    public function persist(RegistrationCode $code): void;

    public function remove(RegistrationCode $code): void;

    public function getPaginatedUsers(int $itemsPerPage, int &$page, ?string $query = null, ?string $grade = null): Paginator;

    public function removeRedeemed(): void;
}