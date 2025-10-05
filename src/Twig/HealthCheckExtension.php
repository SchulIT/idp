<?php

declare(strict_types=1);

namespace App\Twig;

use App\HealthCheck\HealthCheckService;
use Override;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class HealthCheckExtension extends AbstractExtension {

    public function __construct(private readonly HealthCheckService $service)
    {
    }

    #[Override]
    public function getFunctions(): array {
        return [
            new TwigFunction('health_check', $this->healthCheck(...))
        ];
    }

    public function healthCheck(): array {
        return $this->service->runAllCheckReturnNonFine();
    }
}
