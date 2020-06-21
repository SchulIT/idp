<?php

namespace App\Entity;

use MyCLabs\Enum\Enum;

/**
 * @method static ActiveDirectorySyncSourceType Group()
 * @method static ActiveDirectorySyncSourceType Ou()
 */
class ActiveDirectorySyncSourceType extends Enum {
    const Group = 'group';
    const Ou = 'ou';
}