<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

class UserRepository implements UserRepositoryInterface {

    private $em;

    public function __construct(EntityManagerInterface $objectManager) {
        $this->em = $objectManager;
    }

    public function findAll($offset = 0, $limit = null) {
        $qb = $this->em
            ->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->orderBy('u.username', 'asc')
            ->setFirstResult($offset);

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

    public function persist(User $user) {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function remove(User $user) {
        $this->em->remove($user);
        $this->em->flush();
    }

    public function getPaginatedUsers($itemsPerPage, &$page, $type = null, $query = null): Paginator {
        $query = $this->em
            ->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->orderBy('u.username', 'asc');

        if(!empty($q)) {
            $query
                ->andWhere(
                    $query->expr()->orX(
                        'u.username LIKE :query',
                        'u.firstname LIKE :query',
                        'u.lastname LIKE :query',
                        'u.email LIKE :query'
                    )
                )
                ->setParameter('query', '%' . $q . '%');
        }

        if(!empty($type)) {
            $query
                ->andWhere(
                    'u.type = :type'
                )
                ->setParameter('type', $type);
        }

        if(!is_numeric($page) || $page < 1) {
            $page = 1;
        }

        $offset = ($page - 1) * $itemsPerPage;

        $paginator = new Paginator($query);
        $paginator->getQuery()
            ->setMaxResults($itemsPerPage)
            ->setFirstResult($offset);

        return $paginator;
    }
}