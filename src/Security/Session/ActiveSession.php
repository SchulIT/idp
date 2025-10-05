<?php

declare(strict_types=1);

namespace App\Security\Session;

use DateTimeImmutable;
use stdClass;

class ActiveSession {

    public function __construct(public readonly int $userId, public readonly mixed $sessionId, public readonly ?string $userAgent,
                                public readonly DateTimeImmutable $startedAt, public readonly ?string $ipAddress,
                                public readonly bool $isCurrentSession, public stdClass|null $browserInfo) {
    }
}
