<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class FontAwesomeIconPicker extends TextType {

    public function buildView(FormView $view, FormInterface $form, array $options) {
        parent::buildView($view, $form, $options);

        if(!isset($view->vars['attr'])) {
            $view->vars['attr'] = [ ];
        }

        $view->vars['attr']['data-trigger'] = 'icon';
        $view->vars['attr']['data-target'] = '#icon_' . $view->vars['id'];
    }

    public function getBlockPrefix() {
        return 'icon_picker';
    }
}