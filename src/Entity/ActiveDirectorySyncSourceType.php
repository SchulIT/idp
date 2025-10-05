<?php

declare(strict_types=1);

namespace App\Entity;

enum ActiveDirectorySyncSourceType: string {
    case Group = 'group';
    case Ou = 'ou';
}
