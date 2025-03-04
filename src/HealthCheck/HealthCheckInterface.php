<?php

namespace App\HealthCheck;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.health_check')]
interface HealthCheckInterface {
    /**
     * @return HealthCheckResult|HealthCheckResult[]
     */
    public function runCheck(): HealthCheckResult|array;
}