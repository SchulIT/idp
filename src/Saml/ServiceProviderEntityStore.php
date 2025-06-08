<?php

namespace App\Saml;

use App\Entity\SamlServiceProvider;
use App\Repository\ServiceProviderRepositoryInterface;
use LightSaml\Credential\X509Certificate;
use LightSaml\Model\Metadata\AssertionConsumerService;
use LightSaml\Model\Metadata\EntityDescriptor;
use LightSaml\Model\Metadata\KeyDescriptor;
use LightSaml\Model\Metadata\SpSsoDescriptor;
use LightSaml\SamlConstants;
use LightSaml\Store\EntityDescriptor\EntityDescriptorStoreInterface;

/**
 * Helper which is used by LightSAML to retrieve all valid ServiceProviders. These ServiceProviders are loaded from the
 * database.
 */
class ServiceProviderEntityStore implements EntityDescriptorStoreInterface {

    public function __construct(private readonly ServiceProviderRepositoryInterface $repository)
    {
    }

    /**
     * @inheritDoc
     */
    public function get($entityId): ?EntityDescriptor {
        $provider = $this->repository
           ->findOneByEntityId($entityId);

        if(!$provider instanceof SamlServiceProvider) {
            return null;
        }

        return $this->getEntityDescriptor($provider);
    }

    /**
     * @inheritDoc
     */
    public function has($entityId): bool {
        return $this->get($entityId) !== null;
    }

    /**
     * @inheritDoc
     */
    public function all(): array {
        /** @var EntityDescriptor[] $all */
        $all = [ ];

        $serviceProviders = $this->repository
            ->findAll();

        foreach ($serviceProviders as $serviceProvider) {
            if($serviceProvider instanceof SamlServiceProvider) {
                $all[] = $this->getEntityDescriptor($serviceProvider);
            }
        }

        return $all;
    }

    /**
     * Converts a ServiceProvider entity into an entity descriptor for further use within the LightSAML library
     */
    public function getEntityDescriptor(SamlServiceProvider $serviceProvider): EntityDescriptor {
        $entityDescriptor = new EntityDescriptor($serviceProvider->getEntityId());
        $spDescriptor = new SpSsoDescriptor();

        $spDescriptor->addKeyDescriptor($this->getKeyDescriptor($serviceProvider, KeyDescriptor::USE_SIGNING));
        $spDescriptor->addKeyDescriptor($this->getKeyDescriptor($serviceProvider, KeyDescriptor::USE_ENCRYPTION));

        if($serviceProvider->getAcsUrls() !== null) {
            foreach ($serviceProvider->getAcsUrls() as $url) {
                $consumerService = new AssertionConsumerService($url);
                $consumerService->setBinding(SamlConstants::BINDING_SAML2_HTTP_POST);
                $spDescriptor->addAssertionConsumerService($consumerService);

                $consumerService = new AssertionConsumerService($url);
                $consumerService->setBinding(SamlConstants::BINDING_SAML2_HTTP_REDIRECT);
                $spDescriptor->addAssertionConsumerService($consumerService);
            }
        }

        $entityDescriptor->addItem($spDescriptor);
        return $entityDescriptor;
    }

    /**
     * @param string $use
     */
    private function getKeyDescriptor(SamlServiceProvider $serviceProvider, $use): KeyDescriptor {
        $keyDescriptor = new KeyDescriptor();
        $keyDescriptor->setUse($use);
        $certificate = new X509Certificate();
        $certificate->loadPem($serviceProvider->getCertificate());
        $keyDescriptor->setCertificate($certificate);

        return $keyDescriptor;
    }
}