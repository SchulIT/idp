<?php

namespace App\Entity;

use MyCLabs\Enum\Enum;

/**
 * @method static ServiceAttributeType Text()
 * @method static ServiceAttributeType Select()
 */
class ServiceAttributeType extends Enum {
    const Text = 'text';
    const Select = 'select';
}