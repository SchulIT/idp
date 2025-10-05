<?php

namespace App\Command;

use GpsLab\Bundle\GeoIP2Bundle\Command\UpdateDatabaseCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCommand('app:geoip:update')]
#[AsCronTask('@daily')]
readonly class GeoIpUpdateCommand {
    public function __construct(private UpdateDatabaseCommand $innerCommand) {
    }

    public function __invoke(InputInterface $input, OutputInterface $output): int {
        return $this->innerCommand->run($input, $output);
    }
}