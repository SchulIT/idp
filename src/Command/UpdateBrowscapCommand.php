<?php

namespace App\Command;

use BrowscapPHP\Browscap;
use BrowscapPHP\BrowscapUpdater;
use BrowscapPHP\Helper\IniLoaderInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:browscap:update', description: 'Aktualisiert den Browscap Cache')]
class UpdateBrowscapCommand extends Command {

    public function __construct(private readonly BrowscapUpdater $browscapUpdater, string $name = null) {
        parent::__construct($name);


    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);

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