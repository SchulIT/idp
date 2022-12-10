<?php

namespace App\Repository;

use App\Entity\PasswordResetToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class PasswordResetTokenRepository implements PasswordResetTokenRepositoryInterface {

    public function __construct(private EntityManagerInterface $em)
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

    public function findOneByUser(User $user): ?PasswordResetToken {
        return $this->em->getRepository(PasswordResetToken::class)
            ->findOneBy([
                'user' => $user
            ]);
    }

    public function findOneByToken(string $token): ?PasswordResetToken {
        return $this->em->getRepository(PasswordResetToken::class)
            ->findOneBy([
                'token' => $token
            ]);
    }
}