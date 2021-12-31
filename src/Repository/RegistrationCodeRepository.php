<?php

namespace App\Repository;

use App\Entity\RegistrationCode;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class RegistrationCodeRepository implements RegistrationCodeRepositoryInterface {

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    private function createDefaultQueryBuilder(): QueryBuilder {
        return $this->em->createQueryBuilder()
            ->select(['c', 'u'])
            ->from(RegistrationCode::class, 'c')
            ->leftJoin('c.student', 'u');
    }

    public function findOneByCode(string $code): ?RegistrationCode {
        return $this->createDefaultQueryBuilder()
            ->where('c.code = :code')
            ->setParameter('code', $code)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAll(): array {
        return $this->createDefaultQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    public function persist(RegistrationCode $code): void {
        $this->em->persist($code);
        $this->em->flush();
    }

    public function remove(RegistrationCode $code): void {
        $this->em->remove($code);
        $this->em->flush();;
    }

    public function beginTransaction(): void {
        $this->em->beginTransaction();
    }

    public function commit(): void {
        $this->em->commit();
    }

    public function rollBack(): void {
        $this->em->rollback();;
    }

    public function getPaginatedUsers(int $itemsPerPage, int &$page, ?string $query = null, ?string $grade = null): Paginator {
        $qb = $this->createDefaultQueryBuilder();

        if($query !== null) {
            $qb->andWhere($qb->expr()->like('c.code', ':query'))
                ->setParameter('query', '%' . $query . '%');
        }

        if($grade !== null) {
            $qb->andWhere('u.grade = :grade')
                ->setParameter('grade', $grade);
        }

        if(!is_numeric($page) || $page < 1) {
            $page = 1;
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
    public function findAllUuids(): array {
        $qb = $this->em
            ->createQueryBuilder()
            ->select('u.uuid')
            ->from(RegistrationCode::class, 'u');

        return array_map(function(array $item) {
            return $item['uuid'];
        }, $qb->getQuery()->getScalarResult());
    }

    public function removeRedeemed(): void {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->delete(RegistrationCode::class, 'r')
            ->where($qb->expr()->isNotNull('r.redeemingUser'))
            ->getQuery()
            ->execute();
    }

    public function findByGrade(string $grade): array {
        return $this->em->createQueryBuilder()
            ->select(['c', 's'])
            ->from(RegistrationCode::class, 'c')
            ->leftJoin('c.student', 's')
            ->where('s.grade = :grade')
            ->setParameter('grade', $grade)
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAllByStudent(User $user): array {
        return $this->em->createQueryBuilder()
            ->select(['c', 's'])
            ->from(RegistrationCode::class, 'c')
            ->leftJoin('c.student', 's')
            ->where('s.id = :student')
            ->setParameter('student', $user->getId())
            ->getQuery()
            ->getResult();
    }


    /**
     * @inheritDoc
     */
    public function codeForStudentExists(User $user): bool {
        return $this->em->createQueryBuilder()
            ->select('COUNT(c.id)')
            ->from(RegistrationCode::class, 'c')
            ->leftJoin('c.student', 's')
            ->where('s.id = :student')
            ->setParameter('student', $user->getId())
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }
}