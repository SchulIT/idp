<?php

namespace App\Repository;

use App\Entity\ActiveDirectoryUser;
use App\Entity\User;
use App\Entity\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Ramsey\Uuid\Uuid;

class UserRepository implements UserRepositoryInterface {

    private $em;

    public function __construct(EntityManagerInterface $objectManager) {
        $this->em = $objectManager;
    }

    public function findAll($offset = 0, $limit = null, bool $deleted = false) {
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

    public function findUsersByUsernames(array $usernames) {
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

    public function findUsersUpdatedAfter(\DateTime $dateTime, array $usernames = [ ]) {
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
            ->setParameter('username', $username);

        $result = $qb->getQuery()->getResult();

        if(count($result) === 0) {
            return null;
        }

        return $result[0];
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

    public function persist(User $user) {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function remove(User $user) {
        $this->em->remove($user);
        $this->em->flush();
    }

    /**
     * @param int $itemsPerPage
     * @param int $page
     * @param null $type
     * @param null $role
     * @param null $query
     * @param bool $deleted
     * @return Paginator
     */
    public function getPaginatedUsers($itemsPerPage, &$page, $type = null, $role = null, $query = null, bool $deleted = false): Paginator {
        $qb = $this->em
            ->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->orderBy('u.username', 'asc');

        $qbInner = $this->em
            ->createQueryBuilder()
            ->select('uInner.id')
            ->from(User::class, 'uInner')
            ->leftJoin('uInner.userRoles', 'rInner');

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
        return array_map(function(array $item) {
            return $item['objectGuid'];
        },
            $this->em->createQueryBuilder()
                ->select('u.objectGuid')
                ->from(ActiveDirectoryUser::class, 'u')
                ->getQuery()
                ->getScalarResult()
        );
    }

    /**
     * @inheritDoc
     */
    public function findAllUuids($offset = 0, $limit = null) {
        $qb = $this->em
            ->createQueryBuilder()
            ->select('u.uuid')
            ->from(User::class, 'u')
            ->orderBy('u.username', 'asc')
            ->setFirstResult($offset);

        if($limit !== null) {
            $qb->setMaxResults($limit);
        }

        return array_map(function(array $item) {
            return $item['uuid'];
        }, $qb->getQuery()->getScalarResult());
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
}