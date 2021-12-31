<?php

namespace App\HealthCheck;

class IdentityProviderCertificateCheck extends AbstractCertificateHealthCheck {
    private string $certificateFile;

    public function __construct(string $certificateFile) {
        $this->certificateFile = $certificateFile;
    }

    public function runCheck() {
        if(!is_readable($this->certificateFile)) {
            return new HealthCheckResult(
                HealthCheckResultType::Error(),
                'health_check.error',
                'health_check.idp_certificate.not_present'
            );
        }

        try {
            $certificate = file_get_contents($this->certificateFile);

            return $this->checkCertificate($certificate);
        } catch(\Exception $e) {
            return new HealthCheckResult(
                HealthCheckResultType::Error(),
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