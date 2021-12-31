<?php

namespace App\Form;

use Symfony\Component\Form\FormInterface;

trait AttributeDataTrait {
    public function getAttributeData(FormInterface $form): array {
        $children = $form->get('group_attributes');

        $data = [ ];

        foreach($children as $child) {
            $data[$child->getName()] = $child->getData();
        }

        return $data;
    }
}