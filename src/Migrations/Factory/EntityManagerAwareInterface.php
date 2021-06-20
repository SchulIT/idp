<?php

namespace App\Migrations\Factory;

use Doctrine\ORM\EntityManagerInterface;

interface EntityManagerAwareInterface {
    public function setEntityManager(EntityManagerInterface $entityManager);
}