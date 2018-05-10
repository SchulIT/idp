<?php

namespace App\Twig;

use App\Entity\ServiceAttributeUserRoleValue;
use App\Entity\ServiceAttributeUserTypeValue;
use App\Entity\ServiceAttributeValue;
use App\Entity\ServiceAttributeValueInterface;
use Symfony\Component\Translation\TranslatorInterface;

class TwigFilters extends \Twig_Extension {
    private $translator;

    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }

    public function getFilters() {
        return [
            new \Twig_SimpleFilter('attributeSource', [ $this, 'attributeSource' ])
        ];
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