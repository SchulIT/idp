<?php

namespace App\Traits;

trait ArrayTrait {
    protected function makeArrayWithKeys(array $array, \Closure $idFunc) {
        $result = [ ];

        foreach($array as $item) {
            $result[$idFunc($item)] = $item;
        }

        return $result;
    }
}