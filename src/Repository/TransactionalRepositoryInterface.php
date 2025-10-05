<?php

declare(strict_types=1);

namespace App\Repository;

interface TransactionalRepositoryInterface {
    public function beginTransaction(): void;

    public function commit(): void;

    public function rollBack(): void;
}
