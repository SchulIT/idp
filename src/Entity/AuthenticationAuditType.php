<?php

namespace App\Entity;

enum AuthenticationAuditType: string {
    case Login = 'login';
    case SwitchUser = 'switch_user';
    case Logout = 'logout';
    case Error = 'error';
}