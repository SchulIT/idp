<?php

namespace App\Entity;

use MyCLabs\Enum\Enum;

/**
 * @method static ServiceAttributeType Text()
 * @method static ServiceAttributeType Select()
 */
class ServiceAttributeType extends Enum {
    public const Text = 'text';
    public const Select = 'select';
}