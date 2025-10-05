<?php

declare(strict_types=1);

namespace App\Security\ForgotPassword;

enum Reason {
    case ActiveDirectoryUser;
    case NoEmailAddress;
}
