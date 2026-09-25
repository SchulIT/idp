<?php

declare(strict_types=1);

namespace App\Entity;

use Override;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum ActiveDirectorySyncSourceType: string implements TranslatableInterface {
    case Group = 'group';
    case Ou = 'ou';

    #[Override]
    public function trans(TranslatorInterface $translator, ?string $locale = null): string {
        return match($this) {
            self::Group => $translator->trans('label.ad_group'),
            self::Ou => $translator->trans('label.ou'),
        };
    }
}
