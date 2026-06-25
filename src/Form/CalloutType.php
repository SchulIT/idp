<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalloutType extends AbstractType {
    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setRequired('message');
        $resolver->setDefaults([
            'message_parameters' => [ ],
            'type' => 'info',
            'mapped' => false,
            'required' => false,
        ]);
        $resolver->addAllowedValues('type', ['info', 'success', 'warning', 'danger']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void {
        parent::buildView($view, $form, $options);

        $view->vars['message'] = $options['message'];
        $view->vars['message_parameters'] = $options['message_parameters'];
        $view->vars['type'] = $options['type'];
    }

    public function getBlockPrefix(): string {
        return 'callout';
    }
}