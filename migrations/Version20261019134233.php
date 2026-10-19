<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261019134233 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    private array $associations = [ ];

    public function preUp(Schema $schema): void {
        $stmt = $this->connection->executeQuery('SELECT * FROM service_attribute_saml_service_provider');
        while ($row = $stmt->fetchAssociative()) {
            $this->associations[] = $row;
        }
    }

    public function preDown(Schema $schema): void {
        $stmt = $this->connection->executeQuery('SELECT * FROM service_attribute_saml_service_provider');
        while ($row = $stmt->fetchAssociative()) {
            $this->associations[] = $row;
        }
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE oauth2_access_token (identifier CHAR(80) NOT NULL, client VARCHAR(36) NOT NULL, expiry DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', user_identifier VARCHAR(128) DEFAULT NULL, scopes TEXT DEFAULT NULL COMMENT \'(DC2Type:oauth2_scope)\', revoked TINYINT(1) NOT NULL, INDEX IDX_454D9673C7440455 (client), PRIMARY KEY(identifier)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oauth2_authorization_code (identifier CHAR(80) NOT NULL, client VARCHAR(36) NOT NULL, expiry DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', user_identifier VARCHAR(128) DEFAULT NULL, scopes TEXT DEFAULT NULL COMMENT \'(DC2Type:oauth2_scope)\', revoked TINYINT(1) NOT NULL, INDEX IDX_509FEF5FC7440455 (client), PRIMARY KEY(identifier)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oauth2_client (identifier VARCHAR(36) NOT NULL, name VARCHAR(128) NOT NULL, secret VARCHAR(128) DEFAULT NULL, redirect_uris TEXT DEFAULT NULL COMMENT \'(DC2Type:oauth2_redirect_uri)\', grants TEXT DEFAULT NULL COMMENT \'(DC2Type:oauth2_grant)\', scopes TEXT DEFAULT NULL COMMENT \'(DC2Type:oauth2_scope)\', active TINYINT(1) NOT NULL, allow_plain_text_pkce TINYINT(1) DEFAULT 0 NOT NULL, scope_name VARCHAR(32) NOT NULL, UNIQUE INDEX UNIQ_669FF9C9E3A4EE59 (scope_name), PRIMARY KEY(identifier)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oauth2_refresh_token (identifier CHAR(80) NOT NULL, access_token CHAR(80) DEFAULT NULL, expiry DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', revoked TINYINT(1) NOT NULL, INDEX IDX_4DD90732B6A2DD68 (access_token), PRIMARY KEY(identifier)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE openid_connect_relying_party (identifier CHAR(80) NOT NULL, client VARCHAR(36) NOT NULL, post_logout_redirect_uris TEXT DEFAULT NULL COMMENT \'(DC2Type:oauth2_redirect_uri)\', INDEX IDX_B8DD7972C7440455 (client), PRIMARY KEY(identifier)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_attribute_service_provider (service_attribute_id INT UNSIGNED NOT NULL, service_provider_id INT UNSIGNED NOT NULL, INDEX IDX_55624E8E694A5B1F (service_attribute_id), INDEX IDX_55624E8EC6C98E06 (service_provider_id), PRIMARY KEY(service_attribute_id, service_provider_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE oauth2_access_token ADD CONSTRAINT FK_454D9673C7440455 FOREIGN KEY (client) REFERENCES oauth2_client (identifier) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oauth2_authorization_code ADD CONSTRAINT FK_509FEF5FC7440455 FOREIGN KEY (client) REFERENCES oauth2_client (identifier) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oauth2_refresh_token ADD CONSTRAINT FK_4DD90732B6A2DD68 FOREIGN KEY (access_token) REFERENCES oauth2_access_token (identifier) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE openid_connect_relying_party ADD CONSTRAINT FK_B8DD7972C7440455 FOREIGN KEY (client) REFERENCES oauth2_client (identifier) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_attribute_service_provider ADD CONSTRAINT FK_55624E8E694A5B1F FOREIGN KEY (service_attribute_id) REFERENCES service_attribute (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_attribute_service_provider ADD CONSTRAINT FK_55624E8EC6C98E06 FOREIGN KEY (service_provider_id) REFERENCES service_provider (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_attribute_saml_service_provider DROP FOREIGN KEY FK_652DDF40694A5B1F');
        $this->addSql('ALTER TABLE service_attribute_saml_service_provider DROP FOREIGN KEY FK_652DDF4091BBDEDD');
        $this->addSql('DROP TABLE service_attribute_saml_service_provider');
        $this->addSql('ALTER TABLE service_attribute ADD oidc_claim_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE service_provider ADD client_id VARCHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE service_provider ADD CONSTRAINT FK_6BB228A119EB6921 FOREIGN KEY (client_id) REFERENCES oauth2_client (identifier) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_6BB228A119EB6921 ON service_provider (client_id)');

        foreach($this->associations as $association) {
            $this->addSql(
                'INSERT INTO service_attribute_service_provider (service_attribute_id, service_provider_id) VALUES (?, ?)',
                [
                    $association['service_attribute_id'],
                    $association['saml_service_provider_id']
                ]
            );
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service_provider DROP FOREIGN KEY FK_6BB228A119EB6921');
        $this->addSql('CREATE TABLE service_attribute_saml_service_provider (service_attribute_id INT UNSIGNED NOT NULL, saml_service_provider_id INT UNSIGNED NOT NULL, INDEX IDX_652DDF40694A5B1F (service_attribute_id), INDEX IDX_652DDF4091BBDEDD (saml_service_provider_id), PRIMARY KEY(service_attribute_id, saml_service_provider_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE service_attribute_saml_service_provider ADD CONSTRAINT FK_652DDF40694A5B1F FOREIGN KEY (service_attribute_id) REFERENCES service_attribute (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_attribute_saml_service_provider ADD CONSTRAINT FK_652DDF4091BBDEDD FOREIGN KEY (saml_service_provider_id) REFERENCES service_provider (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oauth2_access_token DROP FOREIGN KEY FK_454D9673C7440455');
        $this->addSql('ALTER TABLE oauth2_authorization_code DROP FOREIGN KEY FK_509FEF5FC7440455');
        $this->addSql('ALTER TABLE oauth2_refresh_token DROP FOREIGN KEY FK_4DD90732B6A2DD68');
        $this->addSql('ALTER TABLE openid_connect_relying_party DROP FOREIGN KEY FK_B8DD7972C7440455');
        $this->addSql('ALTER TABLE service_attribute_service_provider DROP FOREIGN KEY FK_55624E8E694A5B1F');
        $this->addSql('ALTER TABLE service_attribute_service_provider DROP FOREIGN KEY FK_55624E8EC6C98E06');
        $this->addSql('DROP TABLE oauth2_access_token');
        $this->addSql('DROP TABLE oauth2_authorization_code');
        $this->addSql('DROP TABLE oauth2_client');
        $this->addSql('DROP TABLE oauth2_refresh_token');
        $this->addSql('DROP TABLE openid_connect_relying_party');
        $this->addSql('DROP TABLE service_attribute_service_provider');
        $this->addSql('ALTER TABLE service_attribute DROP oidc_claim_name');
        $this->addSql('DROP INDEX IDX_6BB228A119EB6921 ON service_provider');
        $this->addSql('ALTER TABLE service_provider DROP client_id');

        foreach($this->associations as $association) {
            $this->addSql(
                'INSERT INTO service_attribute_service_provider (service_attribute_id, saml_service_provider_id) VALUES (?, ?)',
                [
                    $association['service_attribute_id'],
                    $association['service_provider_id']
                ]
            );
        }
    }
}
