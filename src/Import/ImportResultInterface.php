<?php

namespace App\Import;

interface ImportResultInterface {
    /**
     * @return bool
     */
    public function isSuccessful();
}