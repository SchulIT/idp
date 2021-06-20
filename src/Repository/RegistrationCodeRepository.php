<?php

namespace App\Repository;

use App\Entity\RegistrationCode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class RegistrationCodeRepository implements RegistrationCodeRepositoryInterface {

    private $em;

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

    public function findAll() {
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

    public function beginTransaction() {
        $this->em->beginTransaction();
    }

    public function commit() {
        $this->em->commit();
    }

    public function rollBack() {
        $this->em->rollback();;
    }

    public function getPaginatedUsers(int $itemsPerPage, int &$page, ?string $query = null): Paginator {
        $qb = $this->createDefaultQueryBuilder();

        if($query !== null) {
            $qb->andWhere($qb->expr()->like('c.code', ':query'))
                ->setParameter('query', '%' . $query . '%');
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
            ->where($qb->expr()->isNotNull('r.redeemedAt'))
            ->getQuery()
            ->execute();
    }
}