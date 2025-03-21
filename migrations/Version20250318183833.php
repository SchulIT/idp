<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250318183833 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE authentication_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, username VARCHAR(255) DEFAULT NULL, ip_address VARCHAR(255) DEFAULT NULL, ip_country VARCHAR(255) DEFAULT NULL, `type` VARCHAR(255) DEFAULT NULL, message VARCHAR(255) DEFAULT NULL, authenticator_fqcn VARCHAR(255) DEFAULT NULL, token_fqcn VARCHAR(255) DEFAULT NULL, firewall VARCHAR(255) DEFAULT NULL, request_id VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_7863149FD17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE authentication_audit');
    }
}
