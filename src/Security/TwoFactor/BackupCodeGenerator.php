<?php

declare(strict_types=1);

namespace App\Security\TwoFactor;

class BackupCodeGenerator {

    protected function generateCode(): string {
        $code = (string)random_int(100000,999999);
        return str_pad($code, 6, "0");
    }

    public function generateCodes($number = 10): array {
        $codes = [ ];

        for($i = 0; $i < $number; ++$i) {
            $codes[] = $this->generateCode();
        }

        return $codes;
    }
}
