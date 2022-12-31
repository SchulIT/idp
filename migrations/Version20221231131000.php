<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221231131000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function postUp(Schema $schema): void {

    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->connection->executeQuery("ALTER TABLE `active_directory_sync_option` CHANGE COLUMN `source_type` `source_type` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `source`;");
        $this->connection->executeQuery("ALTER TABLE `active_directory_role_sync_option` CHANGE COLUMN `source_type` `source_type` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `source`;");
        $this->connection->executeQuery("ALTER TABLE `active_directory_grade_sync_option` CHANGE COLUMN `source_type` `source_type` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `source`;");
        $this->connection->executeQuery("ALTER TABLE `application` CHANGE COLUMN `scope` `scope` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `name`;");
        $this->connection->executeQuery("ALTER TABLE `service_attribute` CHANGE COLUMN `type` `type` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `saml_attribute_name`;");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
