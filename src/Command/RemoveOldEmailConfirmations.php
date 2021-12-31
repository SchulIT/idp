<?php

namespace App\Command;

use App\Security\EmailConfirmation\ConfirmationManager;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @CronJob("* *\/2 * * *")
 */
class RemoveOldEmailConfirmations extends Command {

    private ConfirmationManager $manager;

    public function __construct(ConfirmationManager $confirmationManager, string $name = null) {
        parent::__construct($name);

        $this->manager = $confirmationManager;
    }

    public function configure() {
        $this
            ->setName('app:remove-confirmations')
            ->setDescription('Removes expired email confirmations.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $style = new SymfonyStyle($input, $output);

        $count = $this->manager->removeOldConfirmations();

        $style->success(sprintf('Successfully deleted %d expired confirmations', $count));

        return 0;
    }
}