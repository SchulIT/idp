<?php

declare(strict_types=1);

namespace App\Command;

use BrowscapPHP\BrowscapUpdater;
use BrowscapPHP\Helper\IniLoaderInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCommand('app:browscap:update', description: 'Aktualisiert den Browscap Cache')]
#[AsCronTask('@daily')]
readonly class UpdateBrowscapCommand {

    public function __construct(private BrowscapUpdater $browscapUpdater) { }

    public function __invoke(SymfonyStyle $io): int {
        $io->section('Aktualisiere Browscap-Cache');

        try {
            $this->browscapUpdater->update(IniLoaderInterface::PHP_INI_FULL);
            $io->success('Cache aktualisiert');
        } catch (Exception $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
