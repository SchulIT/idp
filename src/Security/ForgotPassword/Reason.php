<?php

namespace App\Security\ForgotPassword;

enum Reason {
    case ActiveDirectoryUser;
    case NoEmailAddress;
}