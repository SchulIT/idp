<?php

declare(strict_types=1);

namespace App\HealthCheck;

enum HealthCheckResultType: string {
    case Warning = 'warning';
    case Error = 'error';
    case Fine = 'fine';
}
