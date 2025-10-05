<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\SamlServiceProvider;
use App\Entity\ServiceProviderConfirmation;
use App\Entity\User;
use App\Repository\ServiceProviderConfirmationRepositoryInterface;
use App\Saml\AttributeValueProvider;

class ServiceProviderConfirmationService {

    public function __construct(private readonly AttributeValueProvider $attributeValueProvider, private readonly ServiceProviderConfirmationRepositoryInterface $confirmationRepository)
    {
    }

    public function needsConfirmation(User $user, SamlServiceProvider $serviceProvider): bool {
        $confirmation = $this->confirmationRepository->findOneByUserAndServiceProvider($user, $serviceProvider);

        if(!$confirmation instanceof ServiceProviderConfirmation) {
            return true;
        }

        $confirmedAttributes = $confirmation->getAttributes();
        $currentAttributes = array_keys($this->attributeValueProvider->getValuesForUser($user, $serviceProvider->getEntityId()));

        sort($confirmedAttributes);
        sort($currentAttributes);

        return $confirmedAttributes !== $currentAttributes;
    }

    public function saveConfirmation(User $user, SamlServiceProvider $serviceProvider): void {
        $confirmation = $this->confirmationRepository->findOneByUserAndServiceProvider($user, $serviceProvider);

        if(!$confirmation instanceof ServiceProviderConfirmation) {
            $confirmation = (new ServiceProviderConfirmation())
                ->setUser($user)
                ->setServiceProvider($serviceProvider);
        }

        $currentAttributes = array_keys($this->attributeValueProvider->getValuesForUser($user, $serviceProvider->getEntityId()));
        $confirmation->setAttributes($currentAttributes);

        $this->confirmationRepository->persist($confirmation);
    }
}
