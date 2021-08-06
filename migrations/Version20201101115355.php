<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201101115355 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function postUp(Schema $schema): void {
        $this->connection->exec('UPDATE service_provider SET class = "saml"');
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service_provider ADD class VARCHAR(255) NOT NULL, CHANGE entity_id entity_id VARCHAR(255) DEFAULT NULL, CHANGE acs acs LONGTEXT DEFAULT NULL, CHANGE certificate certificate LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service_provider DROP class, CHANGE entity_id entity_id VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE acs acs LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE certificate certificate LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
