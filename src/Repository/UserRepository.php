<?php

namespace App\Repository;

use App\Entity\ActiveDirectoryUser;
use App\Entity\User;
use App\Entity\UserRole;
use App\Entity\UserType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;

class UserRepository implements UserRepositoryInterface {

    private bool $isInTransaction = false;

    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function beginTransaction(): void {
        $this->em->beginTransaction();
        $this->isInTransaction = true;
    }

    public function commit(): void {
        if(!$this->isInTransaction) {
            return;
        }

        $this->em->flush();
        $this->em->commit();
        $this->isInTransaction = false;
    }

    public function rollBack(): void {
        $this->em->rollback();
    }

    public function findAll(int $offset = 0, int $limit = null, bool $deleted = false): array {
        $qb = $this->em
            ->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->orderBy('u.username', 'asc')
            ->setFirstResult($offset);

        if($deleted === true) {
            $qb->where($qb->expr()->isNotNull('u.deletedAt'));
        } else {
            $qb->where($qb->expr()->isNull('u.deletedAt'));
        }

        if($limit !== null) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    public function findUsersByUsernames(array $usernames): array {
        $qb = $this->em->createQueryBuilder();

        $qb->select(['u', 'a', 'r', 't'])
            ->from(User::class, 'u')
            ->leftJoin('u.attributes', 'a')
            ->leftJoin('u.userRoles', 'r')
            ->leftJoin('u.type', 't')
            ->where('u.username IN (:usernames)')
            ->setParameter('usernames', $usernames);

        return $qb->getQuery()->getResult();
    }

    public function findUsersUpdatedAfter(DateTime $dateTime, array $usernames = [ ]): array {
        $qb = $this->em
            ->createQueryBuilder();

        $qb->select(['DISTINCT u.username'])
            ->from(User::class, 'u')
            ->leftJoin('u.attributes', 'a')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->andX(
                        $qb->expr()->isNotNull('u.updatedAt'),
                        $qb->expr()->gt('u.updatedAt', ':datetime')
                    ),
                    $qb->expr()->andX(
                        $qb->expr()->isNotNull('a.updatedAt'),
                        $qb->expr()->gt('a.updatedAt', ':datetime')
                    )
                )
            )
            ->setParameter('datetime', $dateTime);

        if(count($usernames) > 0) {
            $qb->andWhere('u.username IN (:usernames)')
                ->setParameter('usernames', $usernames);
        }

        $usernames = $qb->getQuery()->getScalarResult();

        return $this->findUsersByUsernames($usernames);
    }

    public function findOneByUsername(string $username): ?User {
        $qb = $this->em->createQueryBuilder();

        $qb->select(['u', 'a', 'r', 't'])
            ->from(User::class, 'u')
            ->leftJoin('u.attributes', 'a')
            ->leftJoin('u.userRoles', 'r')
            ->leftJoin('u.type', 't')
            ->where('u.username = :username')
            ->setParameter('username', $username)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    private function createDefaultQueryBuilder(): QueryBuilder {
        return $this->em
            ->createQueryBuilder()
            ->select(['u', 'a', 'r', 't'])
            ->from(User::class, 'u')
            ->leftJoin('u.attributes', 'a')
            ->leftJoin('u.userRoles', 'r')
            ->leftJoin('u.type', 't');
    }

    /**
     * @inheritDoc
     */
    public function findOneByEmail(string $email): ?User {
        return $this->createDefaultQueryBuilder()
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllNotInUsernamesList(array $usernames, UserType $userType): array {
        $qb = $this->em->createQueryBuilder();

        $qb->select(['u'])
            ->from(User::class, 'u')
            ->leftJoin('u.type', 't')
            ->where('t.id = :type')
            ->andWhere($qb->expr()->notIn('u.username', ':usernames'))
            ->setParameter('usernames', $usernames)
            ->setParameter('type', $userType->getId());

        return $qb->getQuery()->getResult();
    }

    public function persist(User $user): void {
        $this->em->persist($user);
        if($this->isInTransaction === false) {
            $this->em->flush();
        }
    }

    public function remove(User $user): void {
        $this->em->remove($user);
        if($this->isInTransaction === false) {
            $this->em->flush();
        }
    }

    /**
     * @inheritDoc
     */
    public function getPaginatedUsers(int $itemsPerPage, int &$page, ?UserType $type = null, ?UserRole $role = null, ?string $query = null, ?string $grade = null, bool $deleted = false, bool $onlyNotLinked = false): Paginator {
        $qb = $this->em
            ->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->orderBy('u.username', 'asc');

        $qbInner = $this->em
            ->createQueryBuilder()
            ->select('DISTINCT uInner.id')
            ->from(User::class, 'uInner')
            ->leftJoin('uInner.userRoles', 'rInner')
            ->leftJoin('uInner.linkedStudents', 'sInner');

        if(!empty($grade)) {
            $qbInner
                ->andWhere(
                    $qbInner->expr()->orX(
                        'uInner.grade = :grade',
                        'sInner.grade = :grade'
                    )
                );
            $qb->setParameter('grade', $grade);
        }

        if(!empty($query)) {
            $qbInner
                ->andWhere(
                    $qb->expr()->orX(
                        'uInner.username LIKE :query',
                        'uInner.firstname LIKE :query',
                        'uInner.lastname LIKE :query',
                        'uInner.email LIKE :query'
                    )
                );
            $qb->setParameter('query', '%' . $query . '%');
        }

        if($type !== null) {
            $qbInner
                ->andWhere(
                    'u.type = :type'
                );
            $qb->setParameter('type', $type);
        }

        if($role !== null) {
            $qbInner->andWhere(
                'rInner.id = :role'
            );
            $qb->setParameter('role', $role);
        }

        if($deleted === true) {
            $qbInner->andWhere($qb->expr()->isNotNull('u.deletedAt'));
        } else {
            $qbInner->andWhere($qb->expr()->isNull('u.deletedAt'));
        }

        if(!is_numeric($page) || $page < 1) {
            $page = 1;
        }

        $qb->where(
            $qb->expr()->in('u.id', $qbInner->getDQL())
        );

        if($type !== null && $type->getAlias() === 'student' && $onlyNotLinked === true) {
            $qb->andWhere(
                $qb->expr()->in('u.id',
                    $this->em->createQueryBuilder()
                        ->select('uStudentInner.id')
                        ->from(User::class, 'uStudentInner')
                        ->leftJoin('uStudentInner.parents', 'pStudentInner')
                        ->where('pStudentInner.id IS NULL')
                        ->getDQL()
                )
            );
        }

        $offset = ($page - 1) * $itemsPerPage;

        $paginator = new Paginator($qb);
        $paginator->getQuery()
            ->setMaxResults($itemsPerPage)
            ->setFirstResult($offset);

        return $paginator;
    }


    /**
     * @inheritDoc
     */
    public function findActiveDirectoryUserByObjectGuid(string $guid): ?ActiveDirectoryUser {
        return $this->em->getRepository(ActiveDirectoryUser::class)
            ->findOneBy(['objectGuid' => $guid]);
    }

    /**
     * @inheritDoc
     */
    public function findAllActiveDirectoryUsersObjectGuid(): array {
        return array_map(fn(array $item) => $item['objectGuid'],
            $this->em->createQueryBuilder()
                ->select('u.objectGuid')
                ->from(ActiveDirectoryUser::class, 'u')
                ->where('u.deletedAt IS NULL')
                ->getQuery()
                ->getScalarResult()
        );
    }

    public function findAllActiveDirectoryUsers(): array {
        return $this->em
            ->createQueryBuilder()
            ->select('u')
            ->from(ActiveDirectoryUser::class, 'u')
            ->where('u.deletedAt IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function findAllUuids(int $offset = 0, ?int $limit = null, bool $excludeDeleted = false): array {
        $qb = $this->em
            ->createQueryBuilder()
            ->select('u.uuid')
            ->from(User::class, 'u')
            ->orderBy('u.username', 'asc')
            ->setFirstResult($offset);

        if($limit !== null) {
            $qb->setMaxResults($limit);
        }

        if($excludeDeleted === true) {
            $qb->andWhere($qb->expr()->isNull('u.deletedAt'));
        }

        return array_map(fn(array $item) => $item['uuid'], $qb->getQuery()->getScalarResult());
    }

    public function findOneByExternalId(string $externalId): ?User {
        return $this->em
            ->getRepository(User::class)
            ->findOneBy([
                'externalId' => $externalId
            ]);
    }

    public function findOneByUuid(string $uuid): ?User {
        return $this->em
            ->getRepository(User::class)
            ->findOneBy([
                'uuid' => $uuid
            ]);
    }

    public function findOneById(int $id): ?User {
        return $this->em
            ->getRepository(User::class)
            ->findOneBy([
                'id' => $id
            ]);
    }

    /**
     * @inheritDoc
     */
    public function findNextNonProvisionedUsers(int $limit): array {
        return $this->em
            ->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->orderBy('u.createdAt', 'asc')
            ->where('u.isProvisioned = false')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function countUsers(?UserType $userType = null): int {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select('COUNT(u.id)')
            ->from(User::class, 'u')
            ->where($qb->expr()->isNull('u.deletedAt'));

        if($userType !== null) {
            $qb->andWhere('u.type = :type')
                ->setParameter('type', $userType);
        }

        return $qb->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllExternalIdsByExternalIdList(array $externalIds): array {
        if(count($externalIds) === 0) {
            return [];
        }

        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('u.externalId')
            ->from(User::class, 'u')
            ->where($qb->expr()->in('u.externalId', ':ids'))
            ->setParameter('ids', $externalIds);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        return array_map(fn($row) => $row['externalId'], $result);
    }

    /**
     * @inheritDoc
     */
    public function removeDeletedUsers(DateTime $threshold): int {
        $qb = $this->em->createQueryBuilder();

        $qb->delete(User::class, 'u')
            ->where($qb->expr()->isNotNull('u.deletedAt'))
            ->andWhere('u.deletedAt < :threshold')
            ->setParameter('threshold', $threshold);

        return $qb->getQuery()->execute();
    }

    public function findParentUsersWithoutStudents(): array {
        $qbInner = $this->em->createQueryBuilder()
            ->select('uInner.id')
            ->from(User::class, 'uInner')
            ->leftJoin('uInner.type', 'tInner')
            ->leftJoin('uInner.linkedStudents', 'sInner')
            ->where("tInner.alias = 'parent'")
            ->andWhere('sInner.id IS NULL');

        $qb = $this->createDefaultQueryBuilder();

        $qb->where(
            $qb->expr()->in('u.id', $qbInner->getDQL())
        );

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllStudentsWithoutParents(): array {
        $qbInner = $this->em->createQueryBuilder()
            ->select('uInner.id')
            ->from(User::class, 'uInner')
            ->leftJoin('uInner.parents', 'pInner')
            ->leftJoin('uInner.type', 'tInner')
            ->where('pInner.id IS NULL')
            ->andWhere("tInner.alias = 'student'");

        $qb = $this->createDefaultQueryBuilder();

        $qb->where(
            $qb->expr()->in('u.id', $qbInner->getDQL())
        );

        return $qb->getQuery()->getResult();
    }

    public function findGrades(): array {
        $result = $this->em->createQueryBuilder()
            ->select('DISTINCT u.grade')
            ->from(User::class, 'u')
            ->orderBy('u.grade', 'asc')
            ->where('u.grade IS NOT NULL')
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        return array_map(fn($row) => $row['grade'], $result);
    }

    /**
     * @inheritDoc
     */
    public function findStudentsByGrade(string $grade): array {
        return $this->createDefaultQueryBuilder()
            ->andWhere('u.grade = :grade')
            ->setParameter('grade', $grade)
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws Exception
     */
    public function convertToActiveDirectory(User $user, ActiveDirectoryUser $activeDirectoryUser): ActiveDirectoryUser {
        $dbal = $this->em->getConnection();
        $dbal->update('user', [
            'user_principal_name' => $activeDirectoryUser->getUserPrincipalName(),
            'object_guid' => $activeDirectoryUser->getObjectGuid(),
            'ou' => $activeDirectoryUser->getOu(),
            'groups' => json_encode($activeDirectoryUser->getGroups(), JSON_THROW_ON_ERROR),
            'class' => 'ad'
        ], [
            'id' => $user->getId()
        ]);

        $this->em->detach($user);
        $adUser = $this->findOneById($user->getId());

        if(!$adUser instanceof ActiveDirectoryUser) {
            throw new Exception('Failed to convert user.');
        }

        return $adUser;
    }

    public function convertToUser(ActiveDirectoryUser $user): User {
        $dbal = $this->em->getConnection();
        $dbal->update('user', [
            'user_principal_name' => null,
            'object_guid' => null,
            'ou' => null,
            'groups' => null,
            'class' => 'user'
        ], [
            'id' => $user->getId()
        ]);

        $this->em->detach($user);
        return $this->findOneById($user->getId());
    }


}