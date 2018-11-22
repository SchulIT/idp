<?php

namespace App\Repository;

use App\Entity\U2fKey;

interface U2fKeyRepositoryInterface {
    public function persist(U2fKey $key);

    public function remove(U2fKey $key);
}