<?php

namespace App\Command;

use App\Entity\UserType;
use App\Repository\UserTypeRepositoryInterface;
use App\Setup\UserTypesSetup;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

class SetupCommand extends Command {

    private Connection $dbalConnection;
    private UserTypesSetup $userTypeSetup;
    private PdoSessionHandler $pdoSessionHandler;

    public function __construct(Connection $connection, UserTypesSetup $userTypeSetup, PdoSessionHandler $pdoSessionHandler, ?string $name = null) {
        parent::__construct($name);

        $this->dbalConnection = $connection;
        $this->userTypeSetup = $userTypeSetup;
        $this->pdoSessionHandler = $pdoSessionHandler;
    }

    public function configure() {
        $this
            ->setName('app:setup')
            ->setDescription('Runs the initial setup');
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);

        $output->write('Add default user type...');
        $this->addDefaultUserType();
        $output->writeln('<fg=green>OK</>');

        $output->write('Create sessions table...');
        $this->setupSessions();
        $output->writeln('<fg=green>OK</>');

        $output->write('Create remember me table...');
        $this->setupRememberMe();
        $output->writeln('<fg=green>OK</>');

        $io->success('Setup completed');

        return 0;
    }

    private function addDefaultUserType() {
        $this->userTypeSetup->setupDefaultUserTypes();
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

        $this->dbalConnection->executeQuery($sql);
    }

    private function setupSessions() {
        $sql = "SHOW TABLES LIKE 'sessions';";
        $row = $this->dbalConnection->executeQuery($sql);

        if($row->fetchAssociative() === false) {
            $this->pdoSessionHandler->createTable();
        }
    }
}