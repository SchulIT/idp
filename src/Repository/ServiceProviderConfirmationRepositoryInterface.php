<?php

namespace App\Repository;

use App\Entity\ServiceProvider;
use App\Entity\ServiceProviderConfirmation;
use App\Entity\User;

interface ServiceProviderConfirmationRepositoryInterface {
    public function findOneByUserAndServiceProvider(User $user, ServiceProvider $serviceProvider): ?ServiceProviderConfirmation;
}