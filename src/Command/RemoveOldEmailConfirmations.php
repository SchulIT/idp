<?php

namespace App\Command;

use App\Security\EmailConfirmation\ConfirmationManager;
use Shapecode\Bundle\CronBundle\Attribute\AsCronJob;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCronJob(schedule: '* *\/2 * * *')]
#[AsCommand(name: 'app:remove-confirmations', description: 'Löscht abgelaufene E-Mail-Bestätigungen')]
class RemoveOldEmailConfirmations extends Command {

    public function __construct(private readonly ConfirmationManager $manager, string $name = null) {
        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $style = new SymfonyStyle($input, $output);

        $count = $this->manager->removeOldConfirmations();

        $style->success(sprintf('%d abgelaufene Bestätigung(en) gelöscht', $count));

        return 0;
    }
}