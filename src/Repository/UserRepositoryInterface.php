<?php

namespace App\Repository;

use App\Entity\ActiveDirectoryUser;
use App\Entity\User;
use App\Entity\UserRole;
use App\Entity\UserType;
use DateTime;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface UserRepositoryInterface {

    public function beginTransaction(): void;

    public function commit(): void;

    /**
     * @param string[] $usernames
     * @return User[]
     */
    public function findUsersByUsernames(array $usernames): array;

    /**
     * @param string[] $usernames
     * @return User[]
     */
    public function findUsersUpdatedAfter(DateTime $dateTime, array $usernames = [ ]): array;

    public function findOneById(int $id): ?User;

    public function findOneByUsername(string $username): ?User;

    public function findOneByExternalId(string $externalId): ?User;

    public function findOneByUuid(string $uuid): ?User;

    /**
     * Returns all external ids from the given $externalIds which are
     * found in the database.
     *
     * @param string[] $externalIds
     * @return string[]
     */
    public function findAllExternalIdsByExternalIdList(array $externalIds): array;

    public function findActiveDirectoryUserByObjectGuid(string $guid): ?ActiveDirectoryUser;

    /**
     * @return string[]
     */
    public function findAllActiveDirectoryUsersObjectGuid(): array;

    /**
     * @return ActiveDirectoryUser[]
     */
    public function findAllActiveDirectoryUsers(): array;

    public function findOneByEmail(string $email): ?User;

    /**
     * @param bool $deleted Whether or not to return deleted users
     * @return User[]
     */
    public function findAll(int $offset = 0, ?int $limit = null, bool $deleted = false): array;

    /**
     * @return string[]
     */
    public function findAllUuids(int $offset = 0, ?int $limit = null): array;

    /**
     * @return User[]
     */
    public function findNextNonProvisionedUsers(int $limit): array;

    /**
     * @return User[]
     */
    public function findStudentsByGrade(string $grade): array;

    public function countUsers(?UserType $userType = null): int;

    public function persist(User $user): void;

    public function remove(User $user): void;

    public function getPaginatedUsers(int $itemsPerPage, int &$page, ?UserType $type = null, ?UserRole $role = null, ?string $query = null, ?string $grade = null, bool $deleted = false, bool $onlyNotLinked = false): Paginator;

    /**
     * Removes deleted users which are deleted before $threshold.
     *
     * @return int Number of removed users.
     */
    public function removeDeletedUsers(DateTime $threshold): int;

    /**
     * @return User[]
     */
    public function findParentUsersWithoutStudents(): array;

    /**
     * @return User[]
     */
    public function findAllStudentsWithoutParents(): array;

    /**
     * @return string[]
     */
    public function findGrades(): array;

    public function convertToActiveDirectory(User $user, ActiveDirectoryUser $activeDirectoryUser): ActiveDirectoryUser;

    public function convertToUser(ActiveDirectoryUser $user): User;
}