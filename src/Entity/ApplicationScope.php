<?php

namespace App\Entity;

enum ApplicationScope: string {
    case Api = 'api';
    case AdConnect = 'ad_connect';
}