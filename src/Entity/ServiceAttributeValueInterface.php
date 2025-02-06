<?php

namespace App\Entity;

interface ServiceAttributeValueInterface {
    public function getAttribute(): ServiceAttribute;

    /**
     * @return mixed
     */
    public function getValue();
}