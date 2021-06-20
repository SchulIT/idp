<?php

namespace App\Migrations\Factory;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Version\MigrationFactory;
use Doctrine\ORM\EntityManagerInterface;

class MigrationFactoryDecorator implements MigrationFactory {

    private $factory;
    private $entityManager;

    public function __construct(MigrationFactory $factory, EntityManagerInterface $entityManager) {
        $this->factory = $factory;
        $this->entityManager = $entityManager;
    }

    public function createVersion(string $migrationClassName): AbstractMigration {
        $instance = $this->factory->createVersion($migrationClassName);

        if($instance instanceof EntityManagerAwareInterface) {
            $instance->setEntityManager($this->entityManager);
        }

        return $instance;
    }
}