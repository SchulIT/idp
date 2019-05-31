<?php

namespace App\Twig;

use App\Entity\ServiceAttributeUserRoleValue;
use App\Entity\ServiceAttributeUserTypeValue;
use App\Entity\ServiceAttributeValue;
use App\Entity\ServiceAttributeValueInterface;
use App\Service\FriendlyAttributeResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigFilters extends AbstractExtension {
    private $translator;
    private $friendlyAttributeResolver;

    public function __construct(TranslatorInterface $translator, FriendlyAttributeResolver $friendlyAttributeResolver) {
        $this->translator = $translator;
        $this->friendlyAttributeResolver = $friendlyAttributeResolver;
    }

    public function getFilters() {
        return [
            new TwigFunction('attributeSource', [ $this, 'attributeSource' ]),
            new TwigFunction('attributeFriendlyName', [ $this, 'attributeFriendlyName'])
        ];
    }

    public function attributeFriendlyName($attribute) {
        return $this->friendlyAttributeResolver->getFriendlyAttributeName($attribute);
    }

    public function attributeSource(ServiceAttributeValueInterface $attributeValue) {
        if($attributeValue instanceof ServiceAttributeValue) {
            return $this->translator->trans('users.attributes.sources.user');
        } else if($attributeValue instanceof ServiceAttributeUserTypeValue) {
            return sprintf(
                '%s "%s"',
                $this->translator->trans('users.attributes.sources.type'),
                $attributeValue->getUserType()->getName()
            );
        } else if($attributeValue instanceof ServiceAttributeUserRoleValue) {
            return sprintf(
                '%s "%s"',
                $this->translator->trans('users.attributes.sources.role'),
                $attributeValue->getUserRole()->getName()
            );
        }
    }

}