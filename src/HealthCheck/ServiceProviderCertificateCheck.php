<?php

namespace App\HealthCheck;

use Exception;
use App\Entity\SamlServiceProvider;
use App\Repository\ServiceProviderRepositoryInterface;

class ServiceProviderCertificateCheck extends AbstractCertificateHealthCheck {

    public function __construct(private ServiceProviderRepositoryInterface $repository)
    {
    }

    /**
     * @inheritDoc
     */
    public function runCheck(): HealthCheckResult|array {
        $result = [ ];

        foreach($this->repository->findAll() as $serviceProvider) {
            if(!$serviceProvider instanceof SamlServiceProvider) {
                continue;
            }

            try {
                $certificate = $serviceProvider->getCertificate();

                $result = $this->checkCertificate($certificate);
                $result->addMessageParameter('%service_provider%', $serviceProvider->getName());

                if($result->getType()->equals(HealthCheckResultType::Fine()) !== true) {
                    $result->setRoute('edit_service_provider');
                    $result->setRouteParameter([
                        'uuid' => $serviceProvider->getUuid()
                    ]);
                }
            } catch(Exception $e) {
                $result[] = new HealthCheckResult(
                    HealthCheckResultType::Error(),
                    'health_check.error',
                    'health_check.error',
                    [
                        '%exception%' => $e->getMessage()
                    ]
                );
            }
        }

        return $result;
    }

    protected function getEmptyMessage(): string {
        return 'health_check.sp_certificate.empty';
    }

    protected function getInvalidMessage(): string {
        return 'health_check.sp_certificate.invalid';
    }

    protected function getExpiredMessage(): string {
        return 'health_check.sp_certificate.expired';
    }

    protected function getExpiresSoonMessage(): string {
        return 'health_check.sp_certificate.expires_soon';
    }

    protected function getFineMessage(): string {
        return 'health_check.sp_certificate.fine';
    }
}