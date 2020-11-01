<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201101122351 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE service_attribute_saml_service_provider (service_attribute_id INT UNSIGNED NOT NULL, saml_service_provider_id INT UNSIGNED NOT NULL, INDEX IDX_652DDF40694A5B1F (service_attribute_id), INDEX IDX_652DDF4091BBDEDD (saml_service_provider_id), PRIMARY KEY(service_attribute_id, saml_service_provider_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE service_attribute_saml_service_provider ADD CONSTRAINT FK_652DDF40694A5B1F FOREIGN KEY (service_attribute_id) REFERENCES service_attribute (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_attribute_saml_service_provider ADD CONSTRAINT FK_652DDF4091BBDEDD FOREIGN KEY (saml_service_provider_id) REFERENCES service_provider (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE service_attribute_service_provider');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE service_attribute_service_provider (service_attribute_id INT UNSIGNED NOT NULL, service_provider_id INT UNSIGNED NOT NULL, INDEX IDX_55624E8EC6C98E06 (service_provider_id), INDEX IDX_55624E8E694A5B1F (service_attribute_id), PRIMARY KEY(service_attribute_id, service_provider_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE service_attribute_service_provider ADD CONSTRAINT FK_55624E8E694A5B1F FOREIGN KEY (service_attribute_id) REFERENCES service_attribute (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_attribute_service_provider ADD CONSTRAINT FK_55624E8EC6C98E06 FOREIGN KEY (service_provider_id) REFERENCES service_provider (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE service_attribute_saml_service_provider');
    }
}
