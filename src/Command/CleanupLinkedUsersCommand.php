<?php

namespace App\Command;

use App\Repository\UserRepositoryInterface;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @CronJob("0 *\/1 * * *")
 */
class CleanupLinkedUsersCommand extends Command {

    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository, string $name = null) {
        parent::__construct($name);
        $this->userRepository = $userRepository;
    }

    public function configure() {
        $this
            ->setName('app:user:cleanup')
            ->setDescription('Cleanup all external ids to remove duplicates.');
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $style = new SymfonyStyle($input, $output);

        $users = $this->userRepository->findAllLinkedUsers(0, 1000000);
        $this->userRepository->beginTransaction();

        $count = 0;

        foreach($users as $user) {
            $externalIds = explode(',', $user->getExternalId());
            $existingIds = $this->userRepository->findAllExternalIdsByExternalIdList($externalIds);
            $user->setExternalId(implode(',', $existingIds));

            $this->userRepository->persist($user);
            $count++;
        }

        $this->userRepository->commit();

        $style->success(sprintf('Successfully updated %d user(s)', $count));
    }
}