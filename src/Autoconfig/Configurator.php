<?php

namespace App\Autoconfig;

use App\Entity\SamlServiceProvider;
use App\Entity\ServiceAttribute;
use App\Entity\ServiceAttributeType;
use SchulIT\CommonBundle\Autoconfig\Roles\AttributeType;
use SchulIT\CommonBundle\Autoconfig\Roles\RoleConfig;
use SchulIT\CommonBundle\Autoconfig\Saml\SamlConfig;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class Configurator {

    public const string RolesEndpoint = '/roles';
    public const string SamlEndpoint = '/saml';

    public function __construct(
        private HttpClientInterface $client,
        private SerializerInterface $serializer, private AutoconfigureUrlNotSetException $autoconfigureUrlNotSetException
    ) { }

    /**
     * @throws AutoconfigureUrlNotSetException
     * @throws TransportExceptionInterface
     * @throws ExceptionInterface
     * @throws HttpExceptionInterface
     */
    public function configure(SamlServiceProvider $samlServiceProvider): void {
        if(empty($this->autoconfigureUrlNotSetException)) {
            throw new AutoconfigureUrlNotSetException();
        }

        $this->configureService($samlServiceProvider);
        $this->configureRoles($samlServiceProvider);
    }

    private function getUrl(string $baseUrl, string $endpoint): string {
        $baseUrl = rtrim($baseUrl, '/');
        $endpoint = ltrim($endpoint, '/');

        return sprintf('%s/%s', $baseUrl, $endpoint);
    }

    private function getAttributeType(AttributeType $type): ServiceAttributeType {
        return match($type) {
            AttributeType::TEXT => ServiceAttributeType::Text,
            AttributeType::CHOICE => ServiceAttributeType::Select
        };
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ExceptionInterface
     * @throws HttpExceptionInterface
     */
    private function configureRoles(SamlServiceProvider $samlServiceProvider): void {
        $url = $this->getUrl($samlServiceProvider->getAutoconfigureUrl(), self::RolesEndpoint);
        $response = $this->client->request('GET', $url);

        $config = $this->serializer->deserialize(
            $response->getContent(),
            RoleConfig::class,
            'json'
        );

        $rolesAttribute = null;

        foreach($samlServiceProvider->getAttributes() as $attribute) {
            if($attribute->getSamlAttributeName() === $config->attributeConfig->samlAttributeName) {
                $rolesAttribute = $attribute;
            }
        }

        if($rolesAttribute === null) {
            $rolesAttribute = new ServiceAttribute()
                ->setName(sprintf('%s-roles', $samlServiceProvider->getUuidString()))
                ->setSamlAttributeName($config->attributeConfig->samlAttributeName);
            $rolesAttribute->addService($samlServiceProvider);
            $samlServiceProvider->getAttributes()->add($rolesAttribute);
        }

        $rolesAttribute
            ->setLabel($config->attributeConfig->displayName)
            ->setDescription($config->attributeConfig->description)
            ->setIsUserEditEnabled($config->attributeConfig->isUserEditable)
            ->setIsMultipleChoice($config->attributeConfig->isMultipleChoice)
            ->setType($this->getAttributeType($config->attributeConfig->type));

        $roles = [ ];

        /** @var array{'name': string, 'description': string } $role */
        foreach($config->roles as $role) {
            $roles[$role['name']] = $role['description'];
        }

        ksort($roles);

        $rolesAttribute->setOptions($roles);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ExceptionInterface
     * @throws HttpExceptionInterface
     */
    private function configureService(SamlServiceProvider $samlServiceProvider): void {
        $url = $this->getUrl($samlServiceProvider->getAutoconfigureUrl(), self::SamlEndpoint);
        $response = $this->client->request('GET', $url);

        $config = $this->serializer->deserialize(
            $response->getContent(),
            SamlConfig::class,
            'json'
        );

        $samlServiceProvider->setUrl($config->url);
        $samlServiceProvider->setEntityId($config->entityId);
        $samlServiceProvider->setName($config->name);
        $samlServiceProvider->setDescription($config->description);
        $samlServiceProvider->setIcon($config->icon);
        $samlServiceProvider->setCertificate($config->certificate);

        $samlServiceProvider->setAcsUrls($config->acsUrls);
    }
}