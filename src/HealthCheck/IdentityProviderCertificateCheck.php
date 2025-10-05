<?php

declare(strict_types=1);

namespace App\HealthCheck;

use Exception;
class IdentityProviderCertificateCheck extends AbstractCertificateHealthCheck {
    public function __construct(private readonly string $certificateFile)
    {
    }

    public function runCheck(): HealthCheckResult|array {
        if(!is_readable($this->certificateFile)) {
            return new HealthCheckResult(
                HealthCheckResultType::Error,
                'health_check.error',
                'health_check.idp_certificate.empty'
            );
        }

        try {
            $certificate = file_get_contents($this->certificateFile);

            return $this->checkCertificate($certificate);
        } catch(Exception $e) {
            return new HealthCheckResult(
                HealthCheckResultType::Error,
                'health_check.error',
                'health_check.error',
                [
                    '%exception%' => $e->getMessage()
                ]
            );
        }
    }

    protected function getEmptyMessage(): string {
        return 'health_check.idp_certificate.empty';
    }

    protected function getInvalidMessage(): string {
        return 'health_check.idp_certificate.invalid';
    }

    protected function getExpiredMessage(): string {
        return 'health_check.idp_certificate.expired';
    }

    protected function getExpiresSoonMessage(): string {
        return 'health_check.idp_certificate.expires_soon';
    }

    protected function getFineMessage(): string {
        return 'health_check.idp_certificate.fine';
    }
}
