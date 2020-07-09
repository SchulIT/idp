<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200709105141 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE active_directory_grade_sync_option (id INT UNSIGNED AUTO_INCREMENT NOT NULL, grade VARCHAR(32) NOT NULL, source VARCHAR(255) NOT NULL, source_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:ad_source_type)\', uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_1F44A511D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE active_directory_role_sync_option (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_role_id INT UNSIGNED DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, source VARCHAR(255) NOT NULL, source_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:ad_source_type)\', uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_DAE439E75F8A7F73 (source), UNIQUE INDEX UNIQ_DAE439E7D17F50A6 (uuid), INDEX IDX_DAE439E78E0E3CA6 (user_role_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE active_directory_sync_option (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_type_id INT UNSIGNED DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, source VARCHAR(255) NOT NULL, source_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:ad_source_type)\', uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_7F5CE5505F8A7F73 (source), UNIQUE INDEX UNIQ_7F5CE550D17F50A6 (uuid), INDEX IDX_7F5CE5509D419299 (user_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE active_directory_upn_suffix (id INT UNSIGNED AUTO_INCREMENT NOT NULL, suffix VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_44C122CAB5B087DE (suffix), UNIQUE INDEX UNIQ_44C122CAD17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE application (id INT UNSIGNED AUTO_INCREMENT NOT NULL, service_id INT UNSIGNED DEFAULT NULL, name VARCHAR(64) NOT NULL, scope VARCHAR(255) NOT NULL COMMENT \'(DC2Type:application_scope)\', api_key VARCHAR(64) NOT NULL, `description` LONGTEXT NOT NULL, last_activity DATETIME DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_A45BDDC15E237E06 (name), UNIQUE INDEX UNIQ_A45BDDC1C912ED9D (api_key), UNIQUE INDEX UNIQ_A45BDDC1D17F50A6 (uuid), INDEX IDX_A45BDDC1ED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cron_job (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, command VARCHAR(255) NOT NULL, arguments VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, running_instances INT UNSIGNED DEFAULT 0 NOT NULL, max_instances INT UNSIGNED DEFAULT 1 NOT NULL, number INT UNSIGNED DEFAULT 1 NOT NULL, period VARCHAR(255) NOT NULL, last_use DATETIME DEFAULT NULL, next_run DATETIME NOT NULL, enable TINYINT(1) DEFAULT \'1\' NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cron_job_result (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, cron_job_id BIGINT UNSIGNED NOT NULL, run_at DATETIME NOT NULL, run_time DOUBLE PRECISION NOT NULL, status_code INT NOT NULL, output LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_2CD346EE79099ED8 (cron_job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE failure_login_attempt (id INT AUTO_INCREMENT NOT NULL, ip VARCHAR(45) NOT NULL, username VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, data LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX ip (ip), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE id_entity (entity_id VARCHAR(255) NOT NULL, id VARCHAR(255) NOT NULL, expiry DATETIME NOT NULL, PRIMARY KEY(entity_id, id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log (id INT AUTO_INCREMENT NOT NULL, channel VARCHAR(255) NOT NULL, level INT NOT NULL, message LONGTEXT NOT NULL, time DATETIME NOT NULL, details LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE password_reset_token (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED DEFAULT NULL, token VARCHAR(64) NOT NULL, expires_at DATETIME NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_6B7BA4B65F37A13B (token), UNIQUE INDEX UNIQ_6B7BA4B6D17F50A6 (uuid), INDEX IDX_6B7BA4B6A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE privacy_policy (id INT UNSIGNED AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, changed_at DATETIME NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_3EE6A81BD17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE registration_code (id INT UNSIGNED AUTO_INCREMENT NOT NULL, redeeming_user_id INT UNSIGNED DEFAULT NULL, type_id INT UNSIGNED DEFAULT NULL, code VARCHAR(32) NOT NULL, redeemed_at DATETIME DEFAULT NULL, confirmed_at DATETIME DEFAULT NULL, username VARCHAR(255) DEFAULT NULL, username_suffix VARCHAR(255) DEFAULT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, grade VARCHAR(255) DEFAULT NULL, external_id VARCHAR(255) DEFAULT NULL, token VARCHAR(128) DEFAULT NULL, token_created_at DATETIME DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_B82B274477153098 (code), UNIQUE INDEX UNIQ_B82B2744F85E0677 (username), UNIQUE INDEX UNIQ_B82B27445F37A13B (token), UNIQUE INDEX UNIQ_B82B2744D17F50A6 (uuid), UNIQUE INDEX UNIQ_B82B274435D3A765 (redeeming_user_id), INDEX IDX_B82B2744C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_attribute (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(191) NOT NULL, label VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, is_user_edit_enabled TINYINT(1) NOT NULL, saml_attribute_name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:service_attribute_type)\', is_multiple_choice TINYINT(1) NOT NULL, options LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_A2EBD5B15E237E06 (name), UNIQUE INDEX UNIQ_A2EBD5B1D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_attribute_service_provider (service_attribute_id INT UNSIGNED NOT NULL, service_provider_id INT UNSIGNED NOT NULL, INDEX IDX_55624E8E694A5B1F (service_attribute_id), INDEX IDX_55624E8EC6C98E06 (service_provider_id), PRIMARY KEY(service_attribute_id, service_provider_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_attribute_registration_code_value (attribute_id INT UNSIGNED NOT NULL, registration_code_id INT UNSIGNED NOT NULL, value LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\', INDEX IDX_B240DDA8B6E62EFA (attribute_id), INDEX IDX_B240DDA867ABABB1 (registration_code_id), PRIMARY KEY(attribute_id, registration_code_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_attribute_user_role_value (attribute_id INT UNSIGNED NOT NULL, user_role_id INT UNSIGNED NOT NULL, value LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\', INDEX IDX_F3DD8210B6E62EFA (attribute_id), INDEX IDX_F3DD82108E0E3CA6 (user_role_id), PRIMARY KEY(attribute_id, user_role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_attribute_user_type_value (attribute_id INT UNSIGNED NOT NULL, user_type_id INT UNSIGNED NOT NULL, value LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\', INDEX IDX_A0DEDC8B6E62EFA (attribute_id), INDEX IDX_A0DEDC89D419299 (user_type_id), PRIMARY KEY(attribute_id, user_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_attribute_value (attribute_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, value LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_DC4ECB66B6E62EFA (attribute_id), INDEX IDX_DC4ECB66A76ED395 (user_id), PRIMARY KEY(attribute_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_provider (id INT UNSIGNED AUTO_INCREMENT NOT NULL, entity_id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, acs LONGTEXT NOT NULL, url LONGTEXT NOT NULL, certificate LONGTEXT NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_6BB228A181257D5D (entity_id), UNIQUE INDEX UNIQ_6BB228A1D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_provider_confirmation (user_id INT UNSIGNED NOT NULL, service_provider_id INT UNSIGNED NOT NULL, date_time DATETIME NOT NULL, attributes LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_EC395231A76ED395 (user_id), INDEX IDX_EC395231C6C98E06 (service_provider_id), PRIMARY KEY(user_id, service_provider_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE u2f_key (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED DEFAULT NULL, name VARCHAR(255) NOT NULL, key_handle VARCHAR(255) NOT NULL, public_key VARCHAR(255) NOT NULL, certificate LONGTEXT NOT NULL, counter INT NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_9A4369F6D17F50A6 (uuid), INDEX IDX_9A4369F6A76ED395 (user_id), UNIQUE INDEX user_unique (user_id, key_handle), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type_id INT UNSIGNED DEFAULT NULL, username VARCHAR(255) NOT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, password VARCHAR(62) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, grade VARCHAR(255) DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', external_id VARCHAR(255) DEFAULT NULL, is_active TINYINT(1) NOT NULL, is_email_confirmation_pending TINYINT(1) NOT NULL, google_authenticator_secret VARCHAR(255) DEFAULT NULL, backup_codes LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', trusted_version INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, enabled_from DATETIME DEFAULT NULL, enabled_until DATETIME DEFAULT NULL, data LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', privacy_policy_confirmed_at DATETIME DEFAULT NULL, is_provisioned TINYINT(1) NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', deleted_at DATETIME DEFAULT NULL, class VARCHAR(255) NOT NULL, user_principal_name VARCHAR(255) DEFAULT NULL, object_guid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', ou VARCHAR(255) DEFAULT NULL, groups LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649D17F50A6 (uuid), INDEX IDX_8D93D649C54C8C93 (type_id), UNIQUE INDEX UNIQ_8D93D649B67AF3A1 (object_guid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_service_provider (user_id INT UNSIGNED NOT NULL, service_provider_id INT UNSIGNED NOT NULL, INDEX IDX_16A76A7CA76ED395 (user_id), INDEX IDX_16A76A7CC6C98E06 (service_provider_id), PRIMARY KEY(user_id, service_provider_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_user_role (user_id INT UNSIGNED NOT NULL, user_role_id INT UNSIGNED NOT NULL, INDEX IDX_2D084B47A76ED395 (user_id), INDEX IDX_2D084B478E0E3CA6 (user_role_id), PRIMARY KEY(user_id, user_role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_role (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_2DE8C6A3D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_role_service_provider (user_role_id INT UNSIGNED NOT NULL, service_provider_id INT UNSIGNED NOT NULL, INDEX IDX_9D7AFC358E0E3CA6 (user_role_id), INDEX IDX_9D7AFC35C6C98E06 (service_provider_id), PRIMARY KEY(user_role_id, service_provider_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_type (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, alias VARCHAR(255) NOT NULL, edu_person LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', can_change_name TINYINT(1) NOT NULL, can_change_email TINYINT(1) NOT NULL, can_link_students TINYINT(1) NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_F65F1BE0E16C6B94 (alias), UNIQUE INDEX UNIQ_F65F1BE0D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_type_service_provider (user_type_id INT UNSIGNED NOT NULL, service_provider_id INT UNSIGNED NOT NULL, INDEX IDX_FC29E46E9D419299 (user_type_id), INDEX IDX_FC29E46EC6C98E06 (service_provider_id), PRIMARY KEY(user_type_id, service_provider_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE active_directory_role_sync_option ADD CONSTRAINT FK_DAE439E78E0E3CA6 FOREIGN KEY (user_role_id) REFERENCES user_role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE active_directory_sync_option ADD CONSTRAINT FK_7F5CE5509D419299 FOREIGN KEY (user_type_id) REFERENCES user_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC1ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service_provider (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cron_job_result ADD CONSTRAINT FK_2CD346EE79099ED8 FOREIGN KEY (cron_job_id) REFERENCES cron_job (id)');
        $this->addSql('ALTER TABLE password_reset_token ADD CONSTRAINT FK_6B7BA4B6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registration_code ADD CONSTRAINT FK_B82B274435D3A765 FOREIGN KEY (redeeming_user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registration_code ADD CONSTRAINT FK_B82B2744C54C8C93 FOREIGN KEY (type_id) REFERENCES user_type (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE service_attribute_service_provider ADD CONSTRAINT FK_55624E8E694A5B1F FOREIGN KEY (service_attribute_id) REFERENCES service_attribute (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_attribute_service_provider ADD CONSTRAINT FK_55624E8EC6C98E06 FOREIGN KEY (service_provider_id) REFERENCES service_provider (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_attribute_registration_code_value ADD CONSTRAINT FK_B240DDA8B6E62EFA FOREIGN KEY (attribute_id) REFERENCES service_attribute (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_attribute_registration_code_value ADD CONSTRAINT FK_B240DDA867ABABB1 FOREIGN KEY (registration_code_id) REFERENCES registration_code (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_attribute_user_role_value ADD CONSTRAINT FK_F3DD8210B6E62EFA FOREIGN KEY (attribute_id) REFERENCES service_attribute (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_attribute_user_role_value ADD CONSTRAINT FK_F3DD82108E0E3CA6 FOREIGN KEY (user_role_id) REFERENCES user_role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_attribute_user_type_value ADD CONSTRAINT FK_A0DEDC8B6E62EFA FOREIGN KEY (attribute_id) REFERENCES service_attribute (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_attribute_user_type_value ADD CONSTRAINT FK_A0DEDC89D419299 FOREIGN KEY (user_type_id) REFERENCES user_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_attribute_value ADD CONSTRAINT FK_DC4ECB66B6E62EFA FOREIGN KEY (attribute_id) REFERENCES service_attribute (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_attribute_value ADD CONSTRAINT FK_DC4ECB66A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_provider_confirmation ADD CONSTRAINT FK_EC395231A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_provider_confirmation ADD CONSTRAINT FK_EC395231C6C98E06 FOREIGN KEY (service_provider_id) REFERENCES service_provider (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE u2f_key ADD CONSTRAINT FK_9A4369F6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649C54C8C93 FOREIGN KEY (type_id) REFERENCES user_type (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE user_service_provider ADD CONSTRAINT FK_16A76A7CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_service_provider ADD CONSTRAINT FK_16A76A7CC6C98E06 FOREIGN KEY (service_provider_id) REFERENCES service_provider (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_user_role ADD CONSTRAINT FK_2D084B47A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_user_role ADD CONSTRAINT FK_2D084B478E0E3CA6 FOREIGN KEY (user_role_id) REFERENCES user_role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_role_service_provider ADD CONSTRAINT FK_9D7AFC358E0E3CA6 FOREIGN KEY (user_role_id) REFERENCES user_role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_role_service_provider ADD CONSTRAINT FK_9D7AFC35C6C98E06 FOREIGN KEY (service_provider_id) REFERENCES service_provider (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_type_service_provider ADD CONSTRAINT FK_FC29E46E9D419299 FOREIGN KEY (user_type_id) REFERENCES user_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_type_service_provider ADD CONSTRAINT FK_FC29E46EC6C98E06 FOREIGN KEY (service_provider_id) REFERENCES service_provider (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cron_job_result DROP FOREIGN KEY FK_2CD346EE79099ED8');
        $this->addSql('ALTER TABLE service_attribute_registration_code_value DROP FOREIGN KEY FK_B240DDA867ABABB1');
        $this->addSql('ALTER TABLE service_attribute_service_provider DROP FOREIGN KEY FK_55624E8E694A5B1F');
        $this->addSql('ALTER TABLE service_attribute_registration_code_value DROP FOREIGN KEY FK_B240DDA8B6E62EFA');
        $this->addSql('ALTER TABLE service_attribute_user_role_value DROP FOREIGN KEY FK_F3DD8210B6E62EFA');
        $this->addSql('ALTER TABLE service_attribute_user_type_value DROP FOREIGN KEY FK_A0DEDC8B6E62EFA');
        $this->addSql('ALTER TABLE service_attribute_value DROP FOREIGN KEY FK_DC4ECB66B6E62EFA');
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC1ED5CA9E6');
        $this->addSql('ALTER TABLE service_attribute_service_provider DROP FOREIGN KEY FK_55624E8EC6C98E06');
        $this->addSql('ALTER TABLE service_provider_confirmation DROP FOREIGN KEY FK_EC395231C6C98E06');
        $this->addSql('ALTER TABLE user_service_provider DROP FOREIGN KEY FK_16A76A7CC6C98E06');
        $this->addSql('ALTER TABLE user_role_service_provider DROP FOREIGN KEY FK_9D7AFC35C6C98E06');
        $this->addSql('ALTER TABLE user_type_service_provider DROP FOREIGN KEY FK_FC29E46EC6C98E06');
        $this->addSql('ALTER TABLE password_reset_token DROP FOREIGN KEY FK_6B7BA4B6A76ED395');
        $this->addSql('ALTER TABLE registration_code DROP FOREIGN KEY FK_B82B274435D3A765');
        $this->addSql('ALTER TABLE service_attribute_value DROP FOREIGN KEY FK_DC4ECB66A76ED395');
        $this->addSql('ALTER TABLE service_provider_confirmation DROP FOREIGN KEY FK_EC395231A76ED395');
        $this->addSql('ALTER TABLE u2f_key DROP FOREIGN KEY FK_9A4369F6A76ED395');
        $this->addSql('ALTER TABLE user_service_provider DROP FOREIGN KEY FK_16A76A7CA76ED395');
        $this->addSql('ALTER TABLE user_user_role DROP FOREIGN KEY FK_2D084B47A76ED395');
        $this->addSql('ALTER TABLE active_directory_role_sync_option DROP FOREIGN KEY FK_DAE439E78E0E3CA6');
        $this->addSql('ALTER TABLE service_attribute_user_role_value DROP FOREIGN KEY FK_F3DD82108E0E3CA6');
        $this->addSql('ALTER TABLE user_user_role DROP FOREIGN KEY FK_2D084B478E0E3CA6');
        $this->addSql('ALTER TABLE user_role_service_provider DROP FOREIGN KEY FK_9D7AFC358E0E3CA6');
        $this->addSql('ALTER TABLE active_directory_sync_option DROP FOREIGN KEY FK_7F5CE5509D419299');
        $this->addSql('ALTER TABLE registration_code DROP FOREIGN KEY FK_B82B2744C54C8C93');
        $this->addSql('ALTER TABLE service_attribute_user_type_value DROP FOREIGN KEY FK_A0DEDC89D419299');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649C54C8C93');
        $this->addSql('ALTER TABLE user_type_service_provider DROP FOREIGN KEY FK_FC29E46E9D419299');
        $this->addSql('DROP TABLE active_directory_grade_sync_option');
        $this->addSql('DROP TABLE active_directory_role_sync_option');
        $this->addSql('DROP TABLE active_directory_sync_option');
        $this->addSql('DROP TABLE active_directory_upn_suffix');
        $this->addSql('DROP TABLE application');
        $this->addSql('DROP TABLE cron_job');
        $this->addSql('DROP TABLE cron_job_result');
        $this->addSql('DROP TABLE failure_login_attempt');
        $this->addSql('DROP TABLE id_entity');
        $this->addSql('DROP TABLE log');
        $this->addSql('DROP TABLE password_reset_token');
        $this->addSql('DROP TABLE privacy_policy');
        $this->addSql('DROP TABLE registration_code');
        $this->addSql('DROP TABLE service_attribute');
        $this->addSql('DROP TABLE service_attribute_service_provider');
        $this->addSql('DROP TABLE service_attribute_registration_code_value');
        $this->addSql('DROP TABLE service_attribute_user_role_value');
        $this->addSql('DROP TABLE service_attribute_user_type_value');
        $this->addSql('DROP TABLE service_attribute_value');
        $this->addSql('DROP TABLE service_provider');
        $this->addSql('DROP TABLE service_provider_confirmation');
        $this->addSql('DROP TABLE u2f_key');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_service_provider');
        $this->addSql('DROP TABLE user_user_role');
        $this->addSql('DROP TABLE user_role');
        $this->addSql('DROP TABLE user_role_service_provider');
        $this->addSql('DROP TABLE user_type');
        $this->addSql('DROP TABLE user_type_service_provider');
    }
}
