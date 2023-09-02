<?php

namespace App\Command;

use App\Security\ForgotPassword\ForgotPasswordManager;
use Shapecode\Bundle\CronBundle\Attribute\AsCronJob;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCronJob(schedule: '*/10 * * * *')]
#[AsCommand(name: 'app:security:remove_expired_password_reset_tokens', description: 'Löscht abgelaufene Tokens zum Zurücksetzen des Passwortes')]
class RemoveExpiredPasswordRequestTokensCommand extends Command {

    public function __construct(private readonly ForgotPasswordManager $forgotPasswordManager, string $name = null) {
        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);

        $io->section('Lösche alte Tokens');

        $count = $this->forgotPasswordManager->garbageCollect();
        $io->success(sprintf('%d Token gelöscht', $count));

        return 0;
    }
}