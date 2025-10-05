<?php

declare(strict_types=1);

namespace App\Entity;

interface ActiveDirectorySyncOptionInterface {

    public function getSource(): string;

    public function getSourceType(): ActiveDirectorySyncSourceType;
}
