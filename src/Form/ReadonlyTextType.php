<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;

class ReadonlyTextType extends TextType {

    public function getBlockPrefix() {
        return 'readonly_text';
    }

}