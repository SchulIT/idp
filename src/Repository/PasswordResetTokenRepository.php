<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\PasswordResetToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use SchulIT\CommonBundle\Helper\DateHelper;

class PasswordResetTokenRepository implements PasswordResetTokenRepositoryInterface {

    public function __construct(private readonly EntityManagerInterface $em, private readonly DateHelper $dateHelper)
    {
    }

    public function persist(PasswordResetToken $passwordResetToken): void {
        $this->em->persist($passwordResetToken);
        $this->em->flush();
    }

    public function remove(PasswordResetToken $passwordResetToken): void {
        $this->em->remove($passwordResetToken);
        $this->em->flush();
    }

    public function findMostRecentNonExpired(User $user): ?PasswordResetToken {
        return $this->em->createQueryBuilder()
            ->select('t')
            ->from(PasswordResetToken::class, 't')
            ->where('t.user = :user')
            ->andWhere('t.expiresAt > :now')
            ->orderBy('t.expiresAt', 'desc')
            ->setParameter('user', $user)
            ->setParameter('now', $this->dateHelper->getNow())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByToken(string $token): ?PasswordResetToken {
        return $this->em->getRepository(PasswordResetToken::class)
            ->findOneBy([
                'token' => $token
            ]);
    }

    public function removeExpired(): int {
        return $this->em->createQueryBuilder()
            ->delete(PasswordResetToken::class, 't')
            ->where('t.expiresAt < :now')
            ->setParameter('now', $this->dateHelper->getNow())
            ->getQuery()
            ->execute();
    }
}
