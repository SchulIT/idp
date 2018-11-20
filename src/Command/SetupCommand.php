<?php

namespace App\Command;

use App\Entity\UserType;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SetupCommand extends ContainerAwareCommand {
    public function configure() {
        $this
            ->setName('app:setup')
            ->setDescription('Runs the initial setup');
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);

        $this->addDefaultUserType();
        $io->success('Add default user type');

        $this->setupSessions();
        $io->success('Create sessions table');

        $this->setupRememberMe();
        $io->success('Create remember me table');

        $io->success('Setup completed');
    }

    /**
     * @return EntityManager
     */
    private function getEntityManager() {
        return $this->getContainer()->get('doctrine')->getManager();
    }

    private function addDefaultUserType() {
        $userType = (new UserType())
            ->setName('User')
            ->setAlias('user')
            ->setEduPerson(['member']);

        $em = $this->getEntityManager();
        $em->persist($userType);
        $em->flush();
    }

    private function setupRememberMe() {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `rememberme_token` (
    `series`   char(88)     UNIQUE PRIMARY KEY NOT NULL,
    `value`    char(88)     NOT NULL,
    `lastUsed` datetime     NOT NULL,
    `class`    varchar(100) NOT NULL,
    `username` varchar(200) NOT NULL
);
SQL;

        $this->getEntityManager()->getConnection()->exec($sql);
    }

    private function setupSessions() {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `sessions` (
    `sess_id` VARCHAR(128) NOT NULL PRIMARY KEY,
    `sess_data` BLOB NOT NULL,
    `sess_time` INTEGER UNSIGNED NOT NULL,
    `sess_lifetime` MEDIUMINT NOT NULL
) COLLATE utf8_bin, ENGINE = InnoDB;
SQL;

        $this->getEntityManager()->getConnection()->exec($sql);
    }
}