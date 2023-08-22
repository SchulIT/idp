<?php

namespace App\Utils;

class SecurityUtils {
    public static function getRandomHexString(int $length, string $characters = 'abcdefghijklmnopqrstuvwxyz0123456789') {
        if($length < 0) {
            return '';
        }

        $result = '';
        $characterCount = mb_strlen($characters);

        for($i = 0; $i < $length; $i++) {
            $result .= $characters[random_int(0, $characterCount - 1)];
        }

        return $result;
    }
}