<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230821162919 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE email_confirmation DROP valid_until');
        $this->addSql('ALTER TABLE user DROP is_email_confirmation_pending');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD is_email_confirmation_pending TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE email_confirmation ADD valid_until DATETIME NOT NULL');
    }
}
