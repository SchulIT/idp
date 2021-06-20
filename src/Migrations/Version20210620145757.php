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
        /** @var UserType[] $types */
        $types = $this->em->getRepository(UserType::class)
            ->findBy([
                'canLinkStudents' => true
            ]);

        foreach($types as $type) {
            /** @var User[] $sourceUsers */
            $sourceUsers = $this->em->getRepository(User::class)
                ->findBy([
                    'type' => $type
                ]);

            foreach($sourceUsers as $sourceUser) {
                if(empty($sourceUser->getExternalId())) {
                    continue;
                }

                $ids = explode(',', $sourceUser->getExternalId());

                /** @var User[] $targetUsers */
                $targetUsers = $this->em->createQueryBuilder()
                    ->select('u')
                    ->from(User::class, 'u')
                    ->leftJoin('u.type', 't')
                    ->where('u.externalId IN (:ids)')
                    ->andWhere("t.alias = 'student'")
                    ->setParameter('ids', $ids)
                    ->getQuery()
                    ->getResult();

                foreach($targetUsers as $targetUser) {
                    $sourceUser->addLinkedUser($targetUser);
                }

                $sourceUser->setExternalId(null);
                $this->em->persist($sourceUser);
            }
        }

        $this->em->flush();
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
