<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ServiceAttribute;
use App\Repository\ServiceAttributeRepository;
use LightSaml\ClaimTypes;
use SchulIT\CommonBundle\Saml\ClaimTypes as CommonClaimTypes;
use Symfony\Contracts\Translation\TranslatorInterface;

class FriendlyAttributeResolver {
    private ?array $map = null;

    private bool $initialized = false;

    public function __construct(private readonly ServiceAttributeRepository $attributeRepository, private readonly TranslatorInterface $translator)
    {
    }

    private function initialize(): void {
        if($this->initialized) {
            return;
        }

        $map = [
            ClaimTypes::COMMON_NAME => 'attributes.friendly.common_name',
            ClaimTypes::EMAIL_ADDRESS => 'attributes.friendly.email_address',
            ClaimTypes::GIVEN_NAME => 'attributes.friendly.givenname',
            ClaimTypes::SURNAME => 'attributes.friendly.surname',
            CommonClaimTypes::GRADE => 'attributes.friendly.grade',
            CommonClaimTypes::EXTERNAL_ID => 'attributes.friendly.external_id',
            CommonClaimTypes::ID => 'attributes.friendly.id',
            CommonClaimTypes::SERVICES => 'attributes.friendly.services',
            CommonClaimTypes::TYPE => 'attributes.friendly.type',
            'urn:roles' => 'attributes.friendly.roles'
        ];

        /** @var ServiceAttribute[] $attributes */
        $attributes = $this->attributeRepository->getAttributes();

        foreach($attributes as $attribute) {
            $map[$attribute->getName()] = $attribute->getLabel();
        }

        $this->map = $map;
        $this->initialized = true;
    }

    public function getFriendlyAttributeName($attribute) {
        $this->initialize();

        if(array_key_exists($attribute, $this->map)) {
            return $this->translator->trans(
                $this->map[$attribute]
            );
        }

        return $attribute;
    }
}
