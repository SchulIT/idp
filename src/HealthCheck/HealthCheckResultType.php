<?php

namespace App\HealthCheck;

enum HealthCheckResultType: string {
    case Warning = 'warning';
    case Error = 'error';
    case Fine = 'fine';
}