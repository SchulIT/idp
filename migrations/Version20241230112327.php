<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241230112327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function preUp(Schema $schema): void {
        $stmt = $this->connection->executeQuery('SELECT * FROM service_attribute_value');
        while($row = $stmt->fetchAssociative()) {
            $this->addSql('UPDATE service_attribute_value SET `value` = :value WHERE attribute_id = :attribute_id AND user_id = :user_id', [
                'value' => json_encode(unserialize($row['value'])),
                'user_id' => $row['user_id'],
                'attribute_id' => $row['attribute_id']
            ]);
        }

        $stmt = $this->connection->executeQuery('SELECT * FROM service_attribute_user_type_value');
        while($row = $stmt->fetchAssociative()) {
            $this->addSql('UPDATE service_attribute_user_type_value SET `value` = :value WHERE attribute_id = :attribute_id AND user_type_id = :user_type_id', [
                'value' => json_encode(unserialize($row['value'])),
                'user_type_id' => $row['user_type_id'],
                'attribute_id' => $row['attribute_id']
            ]);
        }

        $stmt = $this->connection->executeQuery('SELECT * FROM service_attribute_user_role_value');
        while($row = $stmt->fetchAssociative()) {
            $this->addSql('UPDATE service_attribute_user_role_value SET `value` = :value WHERE attribute_id = :attribute_id AND user_role_id = :user_role_id', [
                'value' => json_encode(unserialize($row['value'])),
                'user_role_id' => $row['user_role_id'],
                'attribute_id' => $row['attribute_id']
            ]);
        }
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service_attribute CHANGE options options JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE service_attribute_user_role_value CHANGE value value JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE service_attribute_user_type_value CHANGE value value JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE service_attribute_value CHANGE value value JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE service_provider_confirmation CHANGE attributes attributes JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE backup_codes backup_codes JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE data data JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE groups groups JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE user_type CHANGE edu_person edu_person JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE available_at available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL, CHANGE available_at available_at DATETIME NOT NULL, CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE backup_codes backup_codes JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE data data JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE groups groups JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE service_attribute CHANGE options options JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE service_attribute_value CHANGE value value LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\'');
        $this->addSql('ALTER TABLE service_attribute_user_role_value CHANGE value value LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\'');
        $this->addSql('ALTER TABLE service_provider_confirmation CHANGE attributes attributes JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE user_type CHANGE edu_person edu_person JSON NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE service_attribute_user_type_value CHANGE value value LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\'');
    }
}
