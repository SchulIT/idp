<?php

namespace App\Service;

use App\Entity\ServiceProvider;
use App\Entity\ServiceProviderConfirmation;
use App\Entity\User;
use App\Repository\ServiceProviderConfirmationRepositoryInterface;
use App\Saml\AttributeValueProvider;

class ServiceProviderConfirmationService {

    private $attributeValueProvider;
    private $confirmationRepository;

    public function __construct(AttributeValueProvider $attributeValueProvider, ServiceProviderConfirmationRepositoryInterface $confirmationRepository) {
        $this->attributeValueProvider = $attributeValueProvider;
        $this->confirmationRepository = $confirmationRepository;
    }

    public function needsConfirmation(User $user, ServiceProvider $serviceProvider) {
        $confirmation = $this->confirmationRepository->findOneByUserAndServiceProvider($user, $serviceProvider);

        if($confirmation === null) {
            return true;
        }

        $confirmedAttributes = $confirmation->getAttributes();
        $currentAttributes = array_keys($this->attributeValueProvider->getValuesForUser($user, $serviceProvider->getEntityId()));

        sort($confirmedAttributes);
        sort($currentAttributes);

        return $confirmedAttributes !== $currentAttributes;
    }

    public function saveConfirmation(User $user, ServiceProvider $serviceProvider) {
        $confirmation = $this->confirmationRepository->findOneByUserAndServiceProvider($user, $serviceProvider);

        if($confirmation === null) {
            $confirmation = (new ServiceProviderConfirmation())
                ->setUser($user)
                ->setServiceProvider($serviceProvider);
        }

        $currentAttributes = array_keys($this->attributeValueProvider->getValuesForUser($user, $serviceProvider->getEntityId()));
        $confirmation->setAttributes($currentAttributes);

        $this->confirmationRepository->persist($confirmation);
    }
}