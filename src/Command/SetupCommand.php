<?php

declare(strict_types=1);

namespace App\Command;

use App\Security\Session\ActiveSessionsResolver;
use App\Setup\UserTypesSetup;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

#[AsCommand(name: 'app:setup', description: 'Runs the initial setup.')]
readonly class SetupCommand {

    public function __construct(private Connection $dbalConnection,
                                private UserTypesSetup $userTypeSetup,
                                private PdoSessionHandler $pdoSessionHandler,
                                private ActiveSessionsResolver $activeSessionsResolver,
                                #[Autowire(param: 'oauth2_private_key')] private string $oauth2PrivateKey,
                                #[Autowire(param: 'oauth2_public_key')] private string $oauth2PublicKey
    ) { }

    public function __invoke(SymfonyStyle $io): int {
        $this->addDefaultUserType($io);
        $this->setupSessions($io);
        $this->setupRememberMe($io);
        $this->setupOauth2($io);

        $io->success('Setup abgeschlossen');

        return Command::SUCCESS;
    }

    private function addDefaultUserType(SymfonyStyle $io): void {
        $io->section('Benutzertypen');
        $io->writeln('Prüfe, ob Benutzertypen existieren und lege sie ggf. an');
        $this->userTypeSetup->setupDefaultUserTypes();
        $io->success('Fertig');
    }

    private function setupRememberMe(SymfonyStyle $io): void {
        $io->section('Erstelle Tabelle für die "angemeldet bleiben"-Funktion, falls nicht vorhanden');
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
        $io->success('Fertig');
    }

    private function setupSessions(SymfonyStyle $io): void {
        $io->section('Sessions');
        $io->write('Prüfe, ob sessions-Tabelle existiert...');
        $sql = "SHOW TABLES LIKE 'sessions';";
        $row = $this->dbalConnection->executeQuery($sql);

        if($row->fetchAssociative() === false) {
            $io->write('Tabelle existiert nicht, lege an...');
            $this->pdoSessionHandler->createTable();
            $io->success('Tabelle angelegt');
        } else {
            $io->success('Tabelle existiert bereits');
        }

        $io->writeln('Prüfe, ob Feld für Daten MEDIUMBLOB ist...');
        // Check if field is of type "MEDIUMBLOB" to allow more session data
        $sql = "SHOW COLUMNS FROM `sessions` WHERE `Field` = 'sess_data'";
        $row = $this->dbalConnection->executeQuery($sql)->fetchAssociative();

        if(strtolower((string) $row['Type']) === 'mediumblob') {
            $io->success('Feld ist bereits MEDIUMBLOB. Fertig.');
        } else {
            $io->writeln(sprintf('Ändere Feld von %s zu MEDIUMBLOB', $row['Type']));
            $sql = "ALTER TABLE `sessions` MODIFY `sess_data` MEDIUMBLOB";
            $this->dbalConnection->executeQuery($sql);
            $io->success('Fertig');
        }

        $io->section('Erstelle Tabelle für Session-Zugehörigkeit');
        $this->activeSessionsResolver->createTable($this->dbalConnection);
        $io->success('Fertig');
    }

    private function setupOauth2(SymfonyStyle $io): void {
        if(file_exists($this->oauth2PrivateKey) && file_exists($this->oauth2PublicKey)) {
            $io->success('Zertifikate für OAuth2/OIDC bereits vorhanden.');
            return;
        } elseif(file_exists($this->oauth2PublicKey)) {
            $io->warning('Es existiert nur der öffentliche Schlüssel für OAuth2/OIDC. Lösche öffentlichen Schlüssen und erstelle sowohl öffentlichen als auch privaten Schlüssel.');
            unlink($this->oauth2PublicKey);
        } else if(file_exists($this->oauth2PrivateKey)) {
            $io->warning('Es existiert nur der öffentliche Schlüssel für OAuth2/OIDC. Lösche öffentlichen Schlüssen und erstelle sowohl öffentlichen als auch privaten Schlüssel.');
            $this->extractPublicKey($io);
            return;
        }

        $openssl = openssl_pkey_new([
            'digest_alg' => 'sha256',
            'private_key_bits' => 4096,
            'private_key_type' => OPENSSL_KEYTYPE_RSA
        ]);

        openssl_pkey_export_to_file($openssl, $this->oauth2PrivateKey);
        $io->success('Privaten Schlüssel erstellt');

        $this->extractPublicKey($io);
    }

    private function extractPublicKey(SymfonyStyle $io): void {
        $privateKey = openssl_get_privatekey(file_get_contents($this->oauth2PrivateKey));
        $details = openssl_pkey_get_details($privateKey);
        $pem = $details['key'];

        file_put_contents($this->oauth2PublicKey, $pem);
        $io->success('Öffentlichen Schlüssel extrahiert.');
    }
}
