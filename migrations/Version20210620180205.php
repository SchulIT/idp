<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210620180205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function preUp(Schema $schema): void {
        $this->connection->executeQuery('DELETE FROM registration_code');
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE registration_code DROP FOREIGN KEY FK_B82B2744C54C8C93');
        $this->addSql('DROP INDEX UNIQ_B82B2744F85E0677 ON registration_code');
        $this->addSql('DROP INDEX IDX_B82B2744C54C8C93 ON registration_code');
        $this->addSql('DROP INDEX UNIQ_B82B27445F37A13B ON registration_code');
        $this->addSql('ALTER TABLE registration_code DROP redeemed_at, DROP confirmed_at, DROP username, DROP username_suffix, DROP firstname, DROP lastname, DROP email, DROP grade, DROP external_id, DROP token, DROP token_created_at, CHANGE type_id student_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE registration_code ADD CONSTRAINT FK_B82B2744CB944F1A FOREIGN KEY (student_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_B82B2744CB944F1A ON registration_code (student_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE registration_code DROP FOREIGN KEY FK_B82B2744CB944F1A');
        $this->addSql('DROP INDEX IDX_B82B2744CB944F1A ON registration_code');
        $this->addSql('ALTER TABLE registration_code ADD redeemed_at DATETIME DEFAULT NULL, ADD confirmed_at DATETIME DEFAULT NULL, ADD username VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD username_suffix VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD firstname VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD lastname VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD grade VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD external_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD token VARCHAR(128) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD token_created_at DATETIME DEFAULT NULL, CHANGE student_id type_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE registration_code ADD CONSTRAINT FK_B82B2744C54C8C93 FOREIGN KEY (type_id) REFERENCES user_type (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B82B2744F85E0677 ON registration_code (username)');
        $this->addSql('CREATE INDEX IDX_B82B2744C54C8C93 ON registration_code (type_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B82B27445F37A13B ON registration_code (token)');
    }
}
