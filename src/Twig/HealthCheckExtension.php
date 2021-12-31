<?php

namespace App\Twig;

use App\HealthCheck\HealthCheckService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class HealthCheckExtension extends AbstractExtension {

    private HealthCheckService $service;

    public function __construct(HealthCheckService $service) {
        $this->service = $service;
    }

    public function getFunctions(): array {
        return [
            new TwigFunction('health_check', [ $this, 'healthCheck' ])
        ];
    }

    public function healthCheck(): array {
        return $this->service->runAllCheckReturnNonFine();
    }
}