<?php

namespace App\HealthCheck;

use MyCLabs\Enum\Enum;

/**
 * @method static HealthCheckResultType Warning()
 * @method static HealthCheckResultType Error()
 * @method static HealthCheckResultType Fine()
 */
class HealthCheckResultType extends Enum {
    private const Warning = 'warning';
    private const Error = 'error';
    private const Fine = 'fine';
}