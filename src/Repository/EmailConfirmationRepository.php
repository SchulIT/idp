<?php

namespace App\Repository;

use App\Entity\EmailConfirmation;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

readonly class EmailConfirmationRepository implements EmailConfirmationRepositoryInterface {
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function findOneByToken(string $token): ?EmailConfirmation {
        return $this->em
            ->getRepository(EmailConfirmation::class)
            ->findOneBy([
                'token' => $token
            ]);
    }

    public function findOneByUser(User $user): ?EmailConfirmation {
        return $this->em->getRepository(EmailConfirmation::class)
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
}