<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\ServiceAttributeUserRoleValue;
use App\Entity\ServiceAttributeUserTypeValue;
use App\Entity\ServiceAttributeValue;
use App\Entity\ServiceAttributeValueInterface;
use App\Service\FriendlyAttributeResolver;
use DateTime;
use Override;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigFilters extends AbstractExtension {
    public function __construct(private readonly TranslatorInterface $translator, private readonly FriendlyAttributeResolver $friendlyAttributeResolver)
    {
    }

    #[Override]
    public function getFilters(): array {
        return [
            new TwigFilter('attributeSource', $this->attributeSource(...)),
            new TwigFilter('attributeFriendlyName', $this->attributeFriendlyName(...)),
            new TwigFilter('from_timestamp', $this->getDateTimeFromTimestamp(...))
        ];
    }

    public function getDateTimeFromTimestamp(int $timestamp): DateTime {
        return (new DateTime())->setTimestamp($timestamp);
    }

    public function attributeFriendlyName($attribute) {
        return $this->friendlyAttributeResolver->getFriendlyAttributeName($attribute);
    }

    public function attributeSource(ServiceAttributeValueInterface $attributeValue): ?string {
        if ($attributeValue instanceof ServiceAttributeValue) {
            return $this->translator->trans('users.attributes.sources.user');
        } elseif ($attributeValue instanceof ServiceAttributeUserTypeValue) {
            return sprintf(
                '%s "%s"',
                $this->translator->trans('users.attributes.sources.type'),
                $attributeValue->getUserType()->getName()
            );
        } elseif ($attributeValue instanceof ServiceAttributeUserRoleValue) {
            return sprintf(
                '%s "%s"',
                $this->translator->trans('users.attributes.sources.role'),
                $attributeValue->getUserRole()->getName()
            );
        }
        return null;
    }

}
