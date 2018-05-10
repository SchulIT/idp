<?php

namespace App\Entity;

interface ServiceAttributeValueInterface {
    /**
     * @return ServiceAttribute
     */
    public function getAttribute();

    /**
     * @return mixed
     */
    public function getValue();
}