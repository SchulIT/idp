<?php

namespace App\Entity;

enum ServiceAttributeType: string {
    case Text = 'text';
    case Select = 'select';
}