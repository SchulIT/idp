<?php

namespace App\HealthCheck;

class HealthCheckService {
    /** @var HealthCheckInterface[] */
    private $checks;

    public function __construct(iterable $checks) {
        foreach($checks as $check) {
            $this->checks[] = $checks;
        }
    }

    /**
     * @return HealthCheckResult[]
     */
    public function runAllChecks() {
        $results = [ ];

        foreach($this->checks as $check) {
            $checkResult = $check->runCheck();

            if($checkResult instanceof HealthCheckResult) {
                $results[] = $checkResult;
            } else if(is_array($checkResult)) {
                $results += array_filter($checkResult, function ($result) { // Ensure correct type
                    return $result instanceof HealthCheckResult;
                });
            }
        }

        return $results;
    }

    /**
     * @return HealthCheckResult[]
     */
    public function runAllCheckReturnNonFine() {
        $results = $this->runAllChecks();

        return array_filter($results, function(HealthCheckResult $checkResult) {
            return $checkResult->getType()->equals(HealthCheckResultType::Fine()) !== true;
        });
    }
}