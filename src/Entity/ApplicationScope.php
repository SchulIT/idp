<?php

namespace App\Entity;

enum ApplicationScope: string {
    case Api = 'api';
    case IdpExchange = 'idp_exchange';
    case AdConnect = 'ad_connect';
}