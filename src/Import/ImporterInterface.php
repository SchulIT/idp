<?php

namespace App\Import;

interface ImporterInterface {
    /**
     * @param string $json
     * @return ImportResultInterface
     */
    public function import($json);
}