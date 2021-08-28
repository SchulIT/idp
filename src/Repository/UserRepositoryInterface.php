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
    public function findUsersByUsernames(array $usernames);

    /**
     * @param \DateTime $dateTime
     * @param string[] $usernames
     * @return User[]
     */
    public function findUsersUpdatedAfter(\DateTime $dateTime, array $usernames = [ ]);

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

    /**
     * @param string $guid
     * @return ActiveDirectoryUser|null
     */
    public function findActiveDirectoryUserByObjectGuid(string $guid): ?ActiveDirectoryUser;

    /**
     * @return string[]
     */
    public function findAllActiveDirectoryUsersObjectGuid(): array;

    /**
     * @param string $email
     * @return User|null
     */
    public function findOneByEmail(string $email): ?User;

    /**
     * @param int $offset
     * @param int|null $limit
     * @param bool $deleted Whether or not to return deleted users
     * @return User[]
     */
    public function findAll($offset = 0, $limit = null, bool $deleted = false);

    /**
     * @param int $offset
     * @param int|null $limit
     * @return string[]
     */
    public function findAllUuids($offset = 0, $limit = null);

    /**
     * @param int $limit
     * @return User[]
     */
    public function findNextNonProvisionedUsers(int $limit): array;

    /**
     * @param string $grade
     * @return User[]
     */
    public function findStudentsByGrade(string $grade): array;

    /**
     * @param UserType|null $userType
     * @return int
     */
    public function countUsers(?UserType $userType = null): int;

    public function persist(User $user);

    public function remove(User $user);

    /**
     * @param int $itemsPerPage
     * @param int $page
     * @param UserType|null $type
     * @param UserRole|null $role
     * @param string|null $query
     * @param string|null $grade
     * @param bool $deleted
     * @return Paginator
     */
    public function getPaginatedUsers(int $itemsPerPage, int &$page, ?UserType $type = null, ?UserRole $role = null, ?string $query = null, ?string $grade = null, bool $deleted = false, bool $onlyNotLinked = false): Paginator;

    /**
     * Removes deleted users which are deleted before $threshold.
     *
     * @param DateTime $threshold
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