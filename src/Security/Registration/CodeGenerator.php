<?php

declare(strict_types=1);

namespace App\Security\Registration;

use App\Entity\RegistrationCode;
use App\Repository\RegistrationCodeRepositoryInterface;

class CodeGenerator {

    private string $characters = 'ABCDEFGHKLMNPQRSTUVWXYZ123456789';
    private int $blocks = 3;
    private int $charactersPerBlock = 4;
    private string $separator = '-';

    public function __construct(private readonly RegistrationCodeRepositoryInterface $codeRepository)
    {
    }

    private function generateRandomCode(): string {
        $code = '';

        for($blockIdx = 0; $blockIdx < $this->blocks; ++$blockIdx) {
            for($charIdx = 0; $charIdx < $this->charactersPerBlock; ++$charIdx) {
                $randomIdx = random_int(0, 1000) % strlen($this->characters);
                $code .= substr($this->characters, $randomIdx, 1);
            }

            if($blockIdx + 1 < $this->blocks) {
                $code .= $this->separator;
            }
        }

        return $code;
    }

    public function generateCode(): string {
        do {
            $code = $this->generateRandomCode();
        } while($this->codeRepository->findOneByCode($code) instanceof RegistrationCode);

        return $code;
    }
}
