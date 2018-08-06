<?php

namespace App\Saml;

use App\Entity\ServiceProvider;
use Doctrine\Common\Persistence\ObjectManager;
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

    private $em;

    public function __construct(ObjectManager $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function get($entityId) {
        /** @var ServiceProvider $provider */
        $provider = $this->em->getRepository(ServiceProvider::class)
           ->findOneByEntityId($entityId);

        if($provider === null) {
            return null;
        }

        return $this->getEntityDescriptor($provider);
    }

    /**
     * @inheritDoc
     */
    public function has($entityId) {
        return $this->get($entityId) !== null;
    }

    /**
     * @inheritDoc
     */
    public function all() {
        /** @var EntityDescriptor[] $all */
        $all = [ ];

        /** @var ServiceProvider[] $serviceProviders */
        $serviceProviders = $this->em->getRepository(ServiceProvider::class)
            ->findAll();

        foreach ($serviceProviders as $serviceProvider) {
            $all[] = $this->getEntityDescriptor($serviceProvider);
        }

        return $all;
    }

    /**
     * Converts a ServiceProvider entity into an entity descriptor for further use within the LightSAML library
     *
     * @param ServiceProvider $serviceProvider
     * @return EntityDescriptor
     */
    public function getEntityDescriptor(ServiceProvider $serviceProvider) {
        $entityDescriptor = new EntityDescriptor($serviceProvider->getEntityId());
        $spDescriptor = new SpSsoDescriptor();

        $spDescriptor->addKeyDescriptor($this->getKeyDescriptor($serviceProvider, KeyDescriptor::USE_SIGNING));
        $spDescriptor->addKeyDescriptor($this->getKeyDescriptor($serviceProvider, KeyDescriptor::USE_ENCRYPTION));

        $consumerService = new AssertionConsumerService($serviceProvider->getAcs());
        $consumerService->setBinding(SamlConstants::BINDING_SAML2_HTTP_POST);
        $spDescriptor->addAssertionConsumerService($consumerService);

        $consumerService = new AssertionConsumerService($serviceProvider->getAcs());
        $consumerService->setBinding(SamlConstants::BINDING_SAML2_HTTP_REDIRECT);
        $spDescriptor->addAssertionConsumerService($consumerService);

        $entityDescriptor->addItem($spDescriptor);
        return $entityDescriptor;
    }

    /**
     * @param ServiceProvider $serviceProvider
     * @param string $use
     * @return KeyDescriptor
     */
    private function getKeyDescriptor(ServiceProvider $serviceProvider, $use) {
        $keyDescriptor = new KeyDescriptor();
        $keyDescriptor->setUse($use);
        $certificate = new X509Certificate();
        $certificate->loadPem($serviceProvider->getCertificate());
        $keyDescriptor->setCertificate($certificate);

        return $keyDescriptor;
    }
}