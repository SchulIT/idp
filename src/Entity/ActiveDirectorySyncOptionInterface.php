<?php

namespace App\Entity;

interface ActiveDirectorySyncOptionInterface {

    public function getSource(): string;

    public function getSourceType(): ActiveDirectorySyncSourceType;
}