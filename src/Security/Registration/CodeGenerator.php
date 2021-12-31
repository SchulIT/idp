<?php

namespace App\Security\Registration;

use App\Repository\RegistrationCodeRepositoryInterface;

class CodeGenerator {

    private string $characters = 'ABCDEFGHKLMNPQRSTUVWXYZ123456789';
    private int $blocks = 3;
    private int $charactersPerBlock = 4;
    private string $separator = '-';

    private RegistrationCodeRepositoryInterface $codeRepository;

    public function __construct(RegistrationCodeRepositoryInterface $codeRepository) {
        $this->codeRepository = $codeRepository;
    }

    private function generateRandomCode(): string {
        $code = '';

        for($blockIdx = 0; $blockIdx < $this->blocks; $blockIdx++) {
            for($charIdx = 0; $charIdx < $this->charactersPerBlock; $charIdx++) {
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
        } while($this->codeRepository->findOneByCode($code) !== null);

        return $code;
    }
}