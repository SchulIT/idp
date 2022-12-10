<?php

namespace App\Repository;

use App\Entity\KioskUser;
use Doctrine\ORM\EntityManagerInterface;

class KioskUserRepository implements KioskUserRepositoryInterface {
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function findOneByToken(string $token): ?KioskUser {
        return $this->em
            ->getRepository(KioskUser::class)
            ->findOneBy([
                'token' => $token
            ]);
    }

    public function findAll(): array {
        return $this->em
            ->createQueryBuilder()
            ->select(['k', 'u'])
            ->from(KioskUser::class, 'k')
            ->leftJoin('k.user', 'u')
            ->orderBy('u.username', 'asc')
            ->getQuery()
            ->getResult();
    }

    public function persist(KioskUser $user): void {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function remove(KioskUser $user): void {
        $this->em->remove($user);
        $this->em->flush();
    }
}