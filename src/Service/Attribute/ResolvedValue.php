<?php

namespace App\Service\Attribute;

use App\Entity\ServiceAttribute;
use App\Entity\ServiceAttributeValueInterface;

readonly class ResolvedValue {

    /**
     * @param ServiceAttribute $attribute
     * @param string|string[] $value
     * @param ServiceAttributeValueInterface[] $sources
     */
    public function __construct(
        public ServiceAttribute $attribute,
        public string|array $value,
        public array $sources,
    ) { }
}