<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250608164157 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    private array $acs = [ ];

    public function preUp(Schema $schema): void {
        $stmt = $this->connection->prepare('SELECT id,acs FROM service_provider');
        $result = $stmt->executeQuery();

        while($row = $result->fetchAssociative()) {
            $this->acs[$row['id']] = $row['acs'];
        }
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE service_provider ADD acs_urls JSON DEFAULT NULL COMMENT '(DC2Type:json)', DROP acs
        SQL);

        foreach($this->acs as $id => $url) {
            $this->addSql('UPDATE service_provider SET acs_urls = ? WHERE id = ?', [ json_encode([$url]), $id ]);
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE service_provider ADD acs LONGTEXT DEFAULT NULL, DROP acs_urls
        SQL);
    }
}
