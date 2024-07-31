<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221231122537 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('SET foreign_key_checks = 0;');
        $this->addSql('ALTER TABLE cron_job_result DROP FOREIGN KEY FK_2CD346EE79099ED8');
        $this->addSql('ALTER TABLE cron_job CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE cron_job_result CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE cron_job_id cron_job_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE cron_job_result ADD CONSTRAINT FK_2CD346EE79099ED8 FOREIGN KEY (cron_job_id) REFERENCES cron_job (id) ON DELETE CASCADE');
        $this->addSql('SET foreign_key_checks = 1;');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('SET foreign_key_checks = 0;');
        $this->addSql('ALTER TABLE cron_job_result DROP FOREIGN KEY FK_2CD346EE79099ED8');
        $this->addSql('ALTER TABLE cron_job_result CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE cron_job_id cron_job_id BIGINT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE cron_job_result ADD CONSTRAINT FK_2CD346EE79099ED8 FOREIGN KEY (cron_job_id) REFERENCES cron_job (id)');
        $this->addSql('ALTER TABLE cron_job CHANGE id id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL');
        $this->addSql('SET foreign_key_checks = 1;');
    }
}
