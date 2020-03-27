<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;

class CodeGeneratorType extends TextType {
    public function getBlockPrefix() {
        return 'code_generator';
    }
}