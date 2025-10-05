<?php

declare(strict_types=1);

namespace App\Entity;

enum ServiceAttributeType: string {
    case Text = 'text';
    case Select = 'select';
}
