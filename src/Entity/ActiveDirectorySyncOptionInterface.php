<?php

namespace App\Entity;

interface ActiveDirectorySyncOptionInterface {

    /**
     * @return string
     */
    public function getSource();

    /**
     * @return ActiveDirectorySyncSourceType
     */
    public function getSourceType(): ActiveDirectorySyncSourceType;
}