<?php

namespace App\Entity;

enum ActiveDirectorySyncSourceType: string {
    case Group = 'group';
    case Ou = 'ou';
}