<?php

namespace App\HealthCheck;

abstract class AbstractCertificateHealthCheck implements HealthCheckInterface {
    protected const CertificateWarningThresholdInDays = 30;

    protected abstract function getEmptyMessage(): string;
    protected abstract function getInvalidMessage(): string;
    protected abstract function getExpiredMessage(): string;
    protected abstract function getExpiresSoonMessage(): string;
    protected abstract function getFineMessage(): string;

    protected function checkCertificate(?string $certificate): HealthCheckResult {
        $now = new \DateTime();

        if(empty($certificate)) {
            return new HealthCheckResult(
                HealthCheckResultType::Error(),
                'health_check.error',
                $this->getEmptyMessage(),
                [ ]
            );
        }

        $cert = openssl_x509_read($certificate);

        if($cert === false) {
            // Error
            $error = openssl_error_string();
            return new HealthCheckResult(
                HealthCheckResultType::Error(),
                'health_check.error',
                $this->getInvalidMessage(),
                [
                    '%openssl%' => $error
                ]
            );
        }

        $certificateInfo = openssl_x509_parse($certificate);
        openssl_x509_free($cert);

        $validTo = (new \DateTime())->setTimestamp($certificateInfo['validTo_time_t']);

        if($validTo < $now) {
            return new HealthCheckResult(
                HealthCheckResultType::Error(),
                'health_check.error',
                $this->getExpiredMessage()
            );
        } else if($validTo < $now->add(new \DateInterval('P' . static::CertificateWarningThresholdInDays . 'D'))) {
            return new HealthCheckResult(
                HealthCheckResultType::Warning(),
                'health_check.error',
                $this->getExpiresSoonMessage()
            );
        } else {
            return new HealthCheckResult(
                HealthCheckResultType::Fine(),
                'health_check.error',
                $this->getFineMessage()
            );
        }
    }
}