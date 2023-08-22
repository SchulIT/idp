<?php

namespace App\Repository;

use App\Entity\EmailConfirmation;
use App\Entity\User;
use DateTime;

interface EmailConfirmationRepositoryInterface {
    public function findOneByToken(string $token): ?EmailConfirmation;

    public function findOneByUser(User $user): ?EmailConfirmation;

    public function persist(EmailConfirmation $confirmation): void;

    public function remove(EmailConfirmation $confirmation): void;
}