<?php

declare(strict_types=1);

namespace App\Saml;

use LightSaml\SamlConstants;
use LightSaml\Context\Profile\AbstractProfileContext;
use LightSaml\Model\Assertion\NameID;
use LightSaml\Provider\EntityDescriptor\EntityDescriptorProviderInterface;
use LightSaml\Provider\NameID\NameIdProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Provider which is used by LightSAML to create the NameId for the current user.
 */
class NameIdProvider implements NameIdProviderInterface {

    public function __construct(private readonly EntityDescriptorProviderInterface $entityDescriptorProvider, private readonly TokenStorageInterface $tokenStorage)
    {
    }

    /**
     * @return NameID|null
     */
    public function getNameID(AbstractProfileContext $context) {
        $token = $this->tokenStorage->getToken();
        /** @var UserInterface $user */
        $user = $token->getUser();

        $nameId = new NameID($user->getUserIdentifier());
        $nameId
            ->setFormat(SamlConstants::NAME_ID_FORMAT_PERSISTENT)
            ->setNameQualifier($this->entityDescriptorProvider->get()->getEntityID());

        return $nameId;
    }
}
