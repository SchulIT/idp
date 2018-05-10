<?php

namespace App\Saml;

use LightSaml\Context\Profile\AbstractProfileContext;
use LightSaml\Model\Assertion\NameID;
use LightSaml\Provider\EntityDescriptor\EntityDescriptorProviderInterface;
use LightSaml\Provider\NameID\NameIdProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class NameIdProvider implements NameIdProviderInterface {

    private $tokenStorage;
    private $entityDescriptorProvider;

    public function __construct(EntityDescriptorProviderInterface $entityDescriptorProvider, TokenStorageInterface $tokenStorage) {
        $this->entityDescriptorProvider = $entityDescriptorProvider;
        $this->tokenStorage = $tokenStorage;
    }

    public function getNameID(AbstractProfileContext $context) {
        $token = $this->tokenStorage->getToken();
        /** @var UserInterface $user */
        $user = $token->getUser();

        $nameId = new NameID($user->getUsername());
        $nameId
            ->setFormat(\LightSaml\SamlConstants::NAME_ID_FORMAT_PERSISTENT)
            ->setNameQualifier($this->entityDescriptorProvider->get()->getEntityID());

        return $nameId;
    }
}