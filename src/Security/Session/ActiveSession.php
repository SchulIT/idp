<?php

namespace App\Security\Session;

use DateTimeImmutable;

class ActiveSession {

    public readonly array $browserInfo;

    public function __construct(public readonly int $userId, public readonly mixed $sessionId, public readonly ?string $userAgent,
                                public readonly DateTimeImmutable $startedAt, public readonly ?string $ipAddress, public readonly bool $isCurrentSession) {
        $browserInfo = [ ];

        if($this->userAgent !== null) {
            $info = @get_browser($this->userAgent, true);

            if($info !== false) {
                $browserInfo = $info;
            }
        }

        $this->browserInfo = $browserInfo;
    }
}