<?php

namespace App\Entity;

use MyCLabs\Enum\Enum;

/**
 * @method static ApplicationScope ImportExport()
 * @method static ApplicationScope IdpExchange()
 * @method static ApplicationScope ReadAttributes()
 */
class ApplicationScope extends Enum {
    public const Api = 'api';
    public const IdpExchange = 'idp_exchange';
    public const ReadAttributes = 'read_attributes';
}