<?php

declare(strict_types=1);

namespace App\Saml;

use App\Entity\SamlServiceProvider;
use App\Entity\ServiceAttribute;
use App\Entity\ServiceAttributeValueInterface;
use App\Entity\ServiceProvider;
use App\Entity\User;
use App\Repository\ServiceAttributeRepository;
use App\Repository\ServiceProviderRepositoryInterface;
use App\Service\AttributeResolver;
use App\Service\UserServiceProviderResolver;
use App\Traits\ArrayTrait;
use Dom\Attr;
use LightSaml\ClaimTypes;
use LightSaml\Model\Assertion\Attribute;
use SchulIT\CommonBundle\Saml\ClaimTypes as ExtendedClaimTypes;
use SchulIT\LightSamlIdpBundle\Provider\Attribute\AbstractAttributeProvider;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Helper class which is used in LightSAML to determine attributes for a user.
 */
class AttributeValueProvider extends AbstractAttributeProvider {

    use ArrayTrait;

    public function __construct(TokenStorageInterface $tokenStorage, private AttributeResolver $attributeResolver, private ServiceAttributeRepository $attributeRepository,
                                private UserServiceProviderResolver $userServiceProviderResolver, private readonly ServiceProviderRepositoryInterface $serviceProviderRepository) {
        parent::__construct($tokenStorage);
    }

    /**
     * Returns a list of common attributes which should always be included in a SAMLResponse
     *
     * @param User|null $user
     */
    public function getCommonAttributesForUser(User|null $user = null): array {
        if(!$user instanceof User) {
            return [ ];
        }

        $attributes = [ ];

        $attributes[ExtendedClaimTypes::ID] = $user->getUuid();
        $attributes[ClaimTypes::SURNAME] = $user->getLastname();
        $attributes[ClaimTypes::GIVEN_NAME] = $user->getFirstname();
        $attributes[ClaimTypes::EMAIL_ADDRESS] = $user->getEmail();
        $attributes[ExtendedClaimTypes::EXTERNAL_ID] =  $user->getExternalId();
        $attributes[ExtendedClaimTypes::SERVICES] = $this->getServices($user);
        $attributes[ExtendedClaimTypes::GRADE] = $user->getGrade();
        $attributes[ExtendedClaimTypes::TYPE] = $user->getType()->getAlias();

        // eduPersonAffiliation
        $attributes[ExtendedClaimTypes::EDU_PERSON_AFFILIATION] = $user->getType()->getEduPerson();

        return $attributes;
    }

    /**
     * @return ServiceAttribute[]
     */
    private function getRequestedAttributes(string $entityId): array {
        $attributes = $this->attributeRepository->getAttributesForServiceProvider($entityId);

        return $this->makeArrayWithKeys($attributes, fn(ServiceAttribute $attribute): ?int => $attribute->getId());
    }

    /**
     * @return ServiceAttributeValueInterface[]
     */
    private function getUserAttributeValues(User $user): array {
        $attributeValues = $this->attributeResolver
            ->getDetailedResultingAttributeValuesForUser($user);

        return $this->makeArrayWithKeys($attributeValues, fn(ServiceAttributeValueInterface $attributeValue): ?int => $attributeValue->getAttribute()->getId());
    }

    /**
     * Returns a list of attributes for the given user and the given entityId (of the requested service provider).
     *
     * @return string[]
     */
    private function getAttributes(string $entityId, User $user): array {
        $attributes = [ ];

        $requestedAttributes = $this->getRequestedAttributes($entityId);
        $userAttributes = $this->getUserAttributeValues($user);

        foreach($requestedAttributes as $attributeId => $requestedAttribute) {
            if(array_key_exists($attributeId, $userAttributes)) {
                $attributes[$requestedAttribute->getSamlAttributeName()] = $userAttributes[$attributeId]->getValue();
            }
        }

        return $attributes;
    }

    /**
     * @param string $entityId
     * @return Attribute[]
     */
    public function getValuesForUser(UserInterface $user, $entityId): array {
        $attributes = [ ];

        $attributes[] = new Attribute(ClaimTypes::COMMON_NAME, $user->getUserIdentifier());

        if(!$user instanceof User) {
            return $this->renameAttributesIfNecessary($entityId, $attributes);
        }

        foreach($this->getCommonAttributesForUser($user) as $name => $value) {
            if(is_string($value)) {
                $value = htmlspecialchars($value);
            }
            
            $attributes[] = new Attribute($name, $value);
        }

        $userAttributes = $this->getAttributes($entityId, $user);

        foreach($userAttributes as $samlAttributeName => $value) {
            $attributes[] = new Attribute($samlAttributeName, $value);
        }

        return $this->renameAttributesIfNecessary($entityId, $attributes);
    }

    /**
     * @param Attribute[] $attributes
     * @return Attribute[]
     */
    private function renameAttributesIfNecessary(string $entityId, array $attributes): array {
        $service = $this->serviceProviderRepository->findOneByEntityId($entityId);

        if($service->getAttributeNameMapping() === []) {
            return $attributes;
        }

        foreach($attributes as $attribute) {
            if(array_key_exists($attribute->getName(), $service->getAttributeNameMapping())) {
                $attribute->setName($service->getAttributeNameMapping()[$attribute->getName()]);
            }
        }

        return $attributes;
    }

    protected function getServices(User $user): array {
        /** @var ServiceProvider[] $services */
        $services = $this->userServiceProviderResolver->getServices($user);

        $attributeValue = [ ];

        foreach($services as $service) {
            $attributeValue[] = json_encode([
                'url' => $service->getUrl(),
                'name' => $service->getName(),
                'description' => $service->getDescription(),
                'icon' => $service->getIcon()
            ], JSON_HEX_AMP | JSON_HEX_TAG);
        }

        return $attributeValue;
    }
}
