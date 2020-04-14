<?php

namespace App\Repository;

use App\Entity\ActiveDirectoryUpnSuffix;
use Doctrine\ORM\EntityManagerInterface;

class ActiveDirectoryUpnSuffixRepository implements ActiveDirectoryUpnSuffixRepositoryInterface {

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array {
        return $this->em->getRepository(ActiveDirectoryUpnSuffix::class)
            ->findAll();
    }

    /**
     * @inheritDoc
     */
    public function persist(ActiveDirectoryUpnSuffix $suffix): void {
        $this->em->persist($suffix);
        $this->em->flush();
    }

    /**
     * @inheritDoc
     */
    public function remove(ActiveDirectoryUpnSuffix $suffix): void {
        $this->em->remove($suffix);
        $this->em->flush();
    }
}