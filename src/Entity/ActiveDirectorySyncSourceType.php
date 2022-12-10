<?php

namespace App\Entity;

use MyCLabs\Enum\Enum;

/**
 * @method static ActiveDirectorySyncSourceType Group()
 * @method static ActiveDirectorySyncSourceType Ou()
 */
class ActiveDirectorySyncSourceType extends Enum {
    public const Group = 'group';
    public const Ou = 'ou';
}