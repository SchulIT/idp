<?php

namespace App\Repository;

use App\Entity\PasswordResetToken;
use App\Entity\User;

interface PasswordResetTokenRepositoryInterface {

    public function findOneByUser(User $user): ?PasswordResetToken;

    public function findOneByToken(string $token): ?PasswordResetToken;

    public function persist(PasswordResetToken $passwordResetToken): void;

    public function remove(PasswordResetToken $passwordResetToken): void;
}