<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250304160539 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DELETE FROM setting');
        $this->addSql('ALTER TABLE setting ADD id INT UNSIGNED AUTO_INCREMENT NOT NULL, ADD `data` JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', DROP value, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9F74B8984E645A7E ON setting (`key`)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DELETE FROM setting');
        $this->addSql('DROP INDEX UNIQ_9F74B8984E645A7E ON setting');
        $this->addSql('ALTER TABLE setting ADD value LONGTEXT NOT NULL COMMENT \'(DC2Type:object)\', DROP id, DROP `data`');
        $this->addSql('ALTER TABLE setting ADD PRIMARY KEY (`key`)');
    }
}
