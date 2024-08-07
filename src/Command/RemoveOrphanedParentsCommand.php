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

#[AsCronJob(schedule: '0 1 * * * ')]
#[AsCommand(name: 'app:user:remove_orphaned', description: 'Löscht Eltern ohne verknüpften Lernenden.')]
class RemoveOrphanedParentsCommand extends Command {

    private const InactiveModifier = '-14 days';

    public function __construct(private readonly DateHelper $dateHelper, private readonly UserRepositoryInterface $userRepository, string $name = null) {
        parent::__construct($name);
    }


    public function execute(InputInterface $input, OutputInterface $output): int {
        $style = new SymfonyStyle($input, $output);

        $users = $this->userRepository->findParentUsersWithoutStudents();
        $this->userRepository->beginTransaction();

        $count = 0;
        $threshold = $this->dateHelper->getToday()->modify(self::InactiveModifier);

        foreach($users as $user) {
            if($user->getLinkedStudents()->count() === 0 && $user->getCreatedAt() < $threshold) {
                $this->userRepository->remove($user);
                $count++;
            }
        }

        $this->userRepository->commit();

        $style->success(sprintf('%d Benutzer gelöscht', $count));

        return Command::SUCCESS;
    }
}