<?php

declare(strict_types=1);

namespace App\Form;

use Override;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextPrefixType extends TextType {
    #[Override]
    public function configureOptions(OptionsResolver $resolver): void {
        parent::configureOptions($resolver);

        $resolver->setRequired('prefix');
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void {
        parent::finishView($view, $form, $options);

        $view->vars['prefix'] = $options['prefix'];
    }

    #[Override]
    public function getBlockPrefix(): string {
        return 'text_prefix';
    }
}
