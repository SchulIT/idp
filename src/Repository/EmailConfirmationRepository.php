<?php

namespace App\Repository;

use App\Entity\EmailConfirmation;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class EmailConfirmationRepository implements EmailConfirmationRepositoryInterface {
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function findOneByToken(string $token): ?EmailConfirmation {
        return $this->em
            ->getRepository(EmailConfirmation::class)
            ->findOneBy([
                'token' => $token
            ]);
    }

    public function findOneByUser(User $user): ?EmailConfirmation {
        return $this->em
            ->getRepository(EmailConfirmation::class)
            ->findOneBy([
                'user' => $user
            ]);
    }

    public function persist(EmailConfirmation $confirmation): void {
        $this->em->persist($confirmation);
        $this->em->flush();
    }

    public function remove(EmailConfirmation $confirmation): void {
        $this->em->remove($confirmation);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    public function removeExpired(DateTime $dateTime): int {
        return $this->em->createQueryBuilder()
            ->delete(EmailConfirmation::class, 'c')
            ->where('c.validUntil < :threshold')
            ->setParameter('threshold', $dateTime)
            ->getQuery()
            ->execute();
    }
}