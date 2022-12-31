<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\User;
use App\Entity\UserType;
use App\Migrations\Factory\EntityManagerAwareInterface;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210620145757 extends AbstractMigration implements EntityManagerAwareInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function setEntityManager(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function postUp(Schema $schema): void {
        return; // This migration is not working anymore due to incompatible entities

        // (but also: it is not needed anymore as there is no active instance that should use this migration)
    }

    public function preDown(Schema $schema): void {

    }

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_links (source_user_id INT UNSIGNED NOT NULL, target_user_id INT UNSIGNED NOT NULL, INDEX IDX_33405A40EEB16BFD (source_user_id), INDEX IDX_33405A406C066AFE (target_user_id), PRIMARY KEY(source_user_id, target_user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_links ADD CONSTRAINT FK_33405A40EEB16BFD FOREIGN KEY (source_user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_links ADD CONSTRAINT FK_33405A406C066AFE FOREIGN KEY (target_user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_links');
    }
}
