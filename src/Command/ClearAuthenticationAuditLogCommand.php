<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\AuthenticationAudit;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCommand('app:auth_audit:clear', description: 'Löscht Einträge aus dem Anmelde-Log anhand der konfigurierten Aufbewahrungsrichtlinie.')]
#[AsCronTask('@daily')]
readonly class ClearAuthenticationAuditLogCommand {
    public function __construct(private int $days, private EntityManagerInterface $em) { }

    public function __invoke(InputInterface $input, OutputInterface $output): int {
        $style = new SymfonyStyle($input, $output);

        if($this->days < 0) {
            $style->error('Parameter AUTH_AUDIT_RETENTION_DAYS (.env.local) muss positiv sein.');
            return Command::FAILURE;
        }

        if($this->days === 0) {
            $style->success('Parameter AUTH_AUDIT_RETENTION_DAYS (.env.local) ist auf 0 gesetzt, d.h. alte Einträge werden NICHT gelöscht.');
            return Command::SUCCESS;
        }

        $threshold = (new DateTime())->sub(new DateInterval('P' . $this->days . 'D'));

        $style->info('Lösche alle Einträge vor ' . $threshold->format('d.m.Y H:i:s'));

        $num = $this->em->createQueryBuilder()
            ->delete(AuthenticationAudit::class, 'a')
            ->where('a.createdAt <= :threshold')
            ->setParameter('threshold', $threshold)
            ->getQuery()
            ->execute();

        $style->success(sprintf('%d Eintrag/Einträge erfolgreich gelöscht', $num));

        return Command::SUCCESS;
    }
}
