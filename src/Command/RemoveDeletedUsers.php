<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\UserRepositoryInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCommand(name: 'app:user:remove_deleted', description: 'Löscht alle zum Löschen vorgemerkten Benutzer, die vor mehr als 30 Tagen zum Löschen markiert wurden.')]
#[AsCronTask('0 0 * * *')]
readonly class RemoveDeletedUsers {
    private const string Modifier = '-30 days';

    public function __construct(private DateHelper $dateHelper, private UserRepositoryInterface $userRepository) {  }

    public function __invoke(SymfonyStyle $style): int {
        $threshold = $this->dateHelper->getToday()->modify(self::Modifier);
        $count = $this->userRepository->removeDeletedUsers($threshold);

        $style->success(sprintf('%d Benutzer gelöscht', $count));

        return Command::SUCCESS;
    }
}
