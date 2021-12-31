<?php

namespace App\Command;

use App\Repository\UserRepositoryInterface;
use DateTime;
use SchulIT\CommonBundle\Helper\DateHelper;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @CronJob("0 0 * * *")
 */
class RemoveDeletedUsers extends Command {

    private DateHelper $dateHelper;
    private UserRepositoryInterface $userRepository;

    private const Modifier = '-30 days';

    public function __construct(DateHelper $dateHelper, UserRepositoryInterface $userRepository, string $name = null) {
        parent::__construct($name);
        $this->dateHelper = $dateHelper;
        $this->userRepository = $userRepository;
    }

    public function configure() {
        $this
            ->setName('app:user:remove_deleted')
            ->setDescription('Cleanup all external ids to remove duplicates.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $style = new SymfonyStyle($input, $output);

        $threshold = $this->dateHelper->getToday()->modify(static::Modifier);
        $count = $this->userRepository->removeDeletedUsers($threshold);

        $style->success(sprintf('Successfully removed %d user(s).', $count));

        return 0;
    }
}