<?php

namespace App\Traits;

use Closure;
trait ArrayTrait {
    /**
     * @return mixed[]
     */
    protected function makeArrayWithKeys(array $array, Closure $idFunc): array {
        $result = [ ];

        foreach($array as $item) {
            $result[$idFunc($item)] = $item;
        }

        return $result;
    }
}