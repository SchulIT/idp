<?php

declare(strict_types=1);

namespace App\Entity;

interface ServiceAttributeValueInterface {
    public function getAttribute(): ServiceAttribute;

    /**
     * @return mixed
     */
    public function getValue();
}
