<?php

namespace App\Tests;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\StringInput;

trait RunCommandTrait {
    protected static function runCommand(Application $application, $command) {
        $application->setAutoExit(false);
        $command = sprintf('%s --quiet', $command);

        return $application->run(new StringInput($command));
    }
}