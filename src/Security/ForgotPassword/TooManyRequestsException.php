<?php

declare(strict_types=1);

namespace App\Security\ForgotPassword;

use Exception;

class TooManyRequestsException extends Exception { }
