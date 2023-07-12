<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230712085500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function preUp(Schema $schema): void {
        parent::preUp($schema);

        $result = $this->connection->executeQuery('SELECT email, COUNT(*) FROM user WHERE email IS NOT NULL GROUP BY email HAVING COUNT(*) > 1');
        foreach($result->fetchAllAssociative() as $row) {
            $this->connection->executeQuery('UPDATE user SET email = NULL WHERE email = ?', [ $row['email'] ]);
        }
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
    }
}
