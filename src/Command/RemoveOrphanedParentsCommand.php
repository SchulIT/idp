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
 * @CronJob("0 1 * * * ")
 */
class RemoveOrphanedParentsCommand extends Command {
    private $dateHelper;
    private $userRepository;

    private const InactiveModifier = '-14 days';

    public function __construct(DateHelper $dateHelper, UserRepositoryInterface $userRepository, string $name = null) {
        parent::__construct($name);
        $this->dateHelper = $dateHelper;
        $this->userRepository = $userRepository;
    }

    public function configure() {
        $this
            ->setName('app:user:remove_orphaned')
            ->setDescription('Cleanup all external ids to remove duplicates.');
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $style = new SymfonyStyle($input, $output);

        $users = $this->userRepository->findAllLinkedUsers(0, 1000000);
        $this->userRepository->beginTransaction();

        $count = 0;
        $threshold = $this->dateHelper->getToday()->modify(static::InactiveModifier);

        foreach($users as $user) {
            if(empty($user->getExternalId()) && $user->getCreatedAt() < $threshold) {
                $this->userRepository->remove($user);
                $count++;
            }
        }

        $this->userRepository->commit();

        $style->success(sprintf('Successfully removed %d user(s)', $count));

        return 0;
    }
}