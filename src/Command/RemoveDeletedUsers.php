<?php

namespace App\Command;

use App\Repository\UserRepositoryInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Shapecode\Bundle\CronBundle\Attribute\AsCronJob;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCronJob(schedule: '0 0 * * *')]
#[AsCommand(name: 'app:user:remove_deleted', description: 'Cleanup all external ids to remove duplicates.')]
class RemoveDeletedUsers extends Command {
    private const Modifier = '-30 days';

    public function __construct(private readonly DateHelper $dateHelper, private readonly UserRepositoryInterface $userRepository, string $name = null) {
        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $style = new SymfonyStyle($input, $output);

        $threshold = $this->dateHelper->getToday()->modify(self::Modifier);
        $count = $this->userRepository->removeDeletedUsers($threshold);

        $style->success(sprintf('Successfully removed %d user(s).', $count));

        return 0;
    }
}