<?php

declare(strict_types=1);

namespace App\HealthCheck;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.health_check')]
interface HealthCheckInterface {
    /**
     * @return HealthCheckResult|HealthCheckResult[]
     */
    public function runCheck(): HealthCheckResult|array;
}
