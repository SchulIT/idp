<?php

namespace App\Repository;

use App\Entity\PrivacyPolicy;

interface PrivacyPolicyRepositoryInterface {
    public function findOne(): ?PrivacyPolicy;

    public function persist(PrivacyPolicy $policy): void;
}