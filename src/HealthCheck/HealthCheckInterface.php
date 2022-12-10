<?php

namespace App\HealthCheck;

interface HealthCheckInterface {
    /**
     * @return HealthCheckResult|HealthCheckResult[]
     */
    public function runCheck(): HealthCheckResult|array;
}