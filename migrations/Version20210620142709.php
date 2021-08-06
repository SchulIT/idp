<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210620142709 extends AbstractMigration
{
    public function postUp(Schema $schema): void {
        $this->connection->executeQuery("UPDATE user_type SET is_built_in = 1 WHERE alias IN ('user', 'student', 'parent', 'teacher', 'secretary')");
    }

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_type ADD is_built_in TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_type DROP is_built_in');
    }
}
