<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210625075607 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function postUp(Schema $schema): void {
        $this->connection->executeQuery("UPDATE user_type SET icon = 'fas fa-user-tie' WHERE alias = 'parent'");
        $this->connection->executeQuery("UPDATE user_type SET icon = 'fas fa-chalkboard-teacher' WHERE alias = 'teacher'");
        $this->connection->executeQuery("UPDATE user_type SET icon = 'fas fa-user-graduate' WHERE alias = 'student'");
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_type ADD icon VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_type DROP icon');
    }
}
