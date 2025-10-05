<?php

declare(strict_types=1);

namespace App\Form;

use Override;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TextCollectionEntryType extends TextType {
    #[Override]
    public function getBlockPrefix(): string {
        return 'text_collection_entry';
    }
}
