<?php

namespace App\Repository;

interface TransactionalRepositoryInterface {
    public function beginTransaction();

    public function commit();

    public function rollBack();
}