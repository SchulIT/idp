<?php

namespace App\Entity;

use MyCLabs\Enum\Enum;

/**
 * @method static ApplicationScope Api()
 * @method static ApplicationScope IdpExchange()
 */
class ApplicationScope extends Enum {
    public const Api = 'api';
    public const IdpExchange = 'idp_exchange';
}