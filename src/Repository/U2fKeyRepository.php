<?php

namespace App\Repository;

use App\Entity\U2fKey;
use Doctrine\ORM\EntityManagerInterface;

class U2fKeyRepository implements U2fKeyRepositoryInterface {

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function persist(U2fKey $key) {
        $this->em->persist($key);
        $this->em->flush();
    }

    public function remove(U2fKey $key) {
        $this->em->remove($key);
        $this->em->flush();
    }
}