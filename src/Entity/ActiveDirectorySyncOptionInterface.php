<?php

namespace App\Entity;

interface ActiveDirectorySyncOptionInterface {

    /**
     * @return string
     */
    public function getSource();

    /**
     * @return string
     */
    public function getSourceType();
}