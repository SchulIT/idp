<?php

namespace App\Security\TwoFactor;

class BackupCodeGenerator {

    protected function generateCode(): string {
        $code = (string)random_int(100000,999999);
        $string = str_pad($code, 6, "0");
        return $string;
    }

    public function generateCodes($number = 10): array {
        $codes = [ ];

        for($i = 0; $i < $number; $i++) {
            $codes[] = $this->generateCode();
        }

        return $codes;
    }
}