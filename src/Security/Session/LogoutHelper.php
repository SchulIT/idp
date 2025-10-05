<?php

declare(strict_types=1);

namespace App\Security\Session;

use App\Entity\User;
use Doctrine\DBAL\Connection;

/**
 * This class helps to logout a user completly by
 * (a) removing all active sessions and
 * (b) removing all remember me tokens
 *
 * Caveat: The user is not logged out from applications. This method only prevents the user
 * from logging in again after a session is expired.
 */
class LogoutHelper {

    public function __construct(private readonly Connection $connection, private readonly ActiveSessionsResolver $activeSessionsResolver) {

    }

    public function logout(User $user): void {
        // Delete active sessions
        $activeSessions = $this->activeSessionsResolver->getSessionsForUser($user);

        foreach($activeSessions as $activeSession) {
            if($activeSession->isCurrentSession === false) {
                $this->connection->delete('sessions', ['sess_id' => $activeSession->sessionId]);
            }
        }

        // Delete rememberme tokens
        $this->connection->delete('rememberme_token', ['username' => $user->getUserIdentifier()]);
    }
}
