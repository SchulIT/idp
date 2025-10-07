<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\UserRepositoryInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCommand(name: 'app:user:remove_orphaned', description: 'Löscht Eltern ohne verknüpften Lernenden.')]
#[AsCronTask('0 1 * * * ')]
readonly class RemoveOrphanedParentsCommand {

    private const string InactiveModifier = '-14 days';

    public function __construct(private DateHelper $dateHelper, private UserRepositoryInterface $userRepository) { }


    public function __invoke(SymfonyStyle $style): int {
        $users = $this->userRepository->findParentUsersWithoutStudents();
        $this->userRepository->beginTransaction();

        $count = 0;
        $threshold = $this->dateHelper->getToday()->modify(self::InactiveModifier);

        foreach($users as $user) {
            if($user->getLinkedStudents()->count() === 0 && $user->getCreatedAt() < $threshold) {
                $this->userRepository->remove($user);
                ++$count;
            }
        }

        $this->userRepository->commit();

        $style->success(sprintf('%d Benutzer gelöscht', $count));

        return Command::SUCCESS;
    }
}
