<?php

declare(strict_types=1);

namespace App\Form;

use Override;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ReadonlyTextType extends TextType {

    #[Override]
    public function getBlockPrefix(): string {
        return 'readonly_text';
    }

}
