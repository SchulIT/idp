<?php

declare(strict_types=1);

namespace App\Command;

use App\Security\ForgotPassword\ForgotPasswordManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCommand(name: 'app:security:remove_expired_password_reset_tokens', description: 'Löscht abgelaufene Tokens zum Zurücksetzen des Passwortes')]
#[AsCronTask('*/10 * * * *')]
readonly class RemoveExpiredPasswordRequestTokensCommand {

    public function __construct(private ForgotPasswordManager $forgotPasswordManager) { }

    public function __invoke(SymfonyStyle $io): int {
        $io->section('Lösche alte Tokens');

        $count = $this->forgotPasswordManager->garbageCollect();
        $io->success(sprintf('%d Token gelöscht', $count));

        return Command::SUCCESS;
    }
}
