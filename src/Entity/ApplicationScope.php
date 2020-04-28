<?php

namespace App\Entity;

use MyCLabs\Enum\Enum;

/**
 * @method static ApplicationScope Api()
 * @method static ApplicationScope IdpExchange()
 * @method static ApplicationScope AdConnect()
 */
class ApplicationScope extends Enum {
    public const Api = 'api';
    public const IdpExchange = 'idp_exchange';
    public const AdConnect = 'ad_connect';
}