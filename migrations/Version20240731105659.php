<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240731105659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service_attribute_registration_code_value DROP FOREIGN KEY FK_B240DDA867ABABB1');
        $this->addSql('ALTER TABLE service_attribute_registration_code_value DROP FOREIGN KEY FK_B240DDA8B6E62EFA');
        $this->addSql('DROP TABLE service_attribute_registration_code_value');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE service_attribute_registration_code_value (attribute_id INT UNSIGNED NOT NULL, registration_code_id INT UNSIGNED NOT NULL, value LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:object)\', INDEX IDX_B240DDA8B6E62EFA (attribute_id), INDEX IDX_B240DDA867ABABB1 (registration_code_id), PRIMARY KEY(attribute_id, registration_code_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE service_attribute_registration_code_value ADD CONSTRAINT FK_B240DDA867ABABB1 FOREIGN KEY (registration_code_id) REFERENCES registration_code (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_attribute_registration_code_value ADD CONSTRAINT FK_B240DDA8B6E62EFA FOREIGN KEY (attribute_id) REFERENCES service_attribute (id) ON DELETE CASCADE');
    }
}
