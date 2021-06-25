<?php

namespace App\Tests\Security\ForgotPassword;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @method bool isPasswordValid(PasswordAuthenticatedUserInterface $user, string $plainPassword)
 * @method bool needsRehash(PasswordAuthenticatedUserInterface $user)
 */
class PasswordHasher implements UserPasswordHasherInterface {

    public function hashPassword(PasswordAuthenticatedUserInterface $user, string $plainPassword) { }
}