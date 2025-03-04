<?php

namespace App\HealthCheck;

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class HealthCheckService {
    /** @var HealthCheckInterface[] */
    private array $checks;

    public function __construct(#[AutowireIterator('app.health_check')] iterable $checks) {
        foreach($checks as $check) {
            $this->checks[] = $check;
        }
    }

    /**
     * @return HealthCheckResult[]
     */
    public function runAllChecks(): array {
        $results = [ ];

        foreach($this->checks as $check) {
            $checkResult = $check->runCheck();

            if($checkResult instanceof HealthCheckResult) {
                $results[] = $checkResult;
            } else if(is_array($checkResult)) {
                $results += array_filter($checkResult, fn($result) => // Ensure correct type
$result instanceof HealthCheckResult);
            }
        }

        return $results;
    }

    /**
     * @return HealthCheckResult[]
     */
    public function runAllCheckReturnNonFine(): array {
        $results = $this->runAllChecks();

        return array_filter($results, fn(HealthCheckResult $checkResult) => $checkResult->getType() !== HealthCheckResultType::Fine);
    }
}