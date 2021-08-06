<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210620193607 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE registration_code DROP INDEX UNIQ_B82B274435D3A765, ADD INDEX IDX_B82B274435D3A765 (redeeming_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE registration_code DROP INDEX IDX_B82B274435D3A765, ADD UNIQUE INDEX UNIQ_B82B274435D3A765 (redeeming_user_id)');
    }
}
