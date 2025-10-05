<?php

declare(strict_types=1);

namespace App\Migrations\Factory;

use Doctrine\ORM\EntityManagerInterface;

interface EntityManagerAwareInterface {
    public function setEntityManager(EntityManagerInterface $entityManager);
}
