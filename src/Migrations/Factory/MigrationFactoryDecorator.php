<?php

declare(strict_types=1);

namespace App\Migrations\Factory;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Version\MigrationFactory;
use Doctrine\ORM\EntityManagerInterface;

class MigrationFactoryDecorator implements MigrationFactory {

    public function __construct(private readonly MigrationFactory $factory, private readonly EntityManagerInterface $entityManager)
    {
    }

    public function createVersion(string $migrationClassName): AbstractMigration {
        $instance = $this->factory->createVersion($migrationClassName);

        if($instance instanceof EntityManagerAwareInterface) {
            $instance->setEntityManager($this->entityManager);
        }

        return $instance;
    }
}
