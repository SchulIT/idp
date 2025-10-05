<?php

declare(strict_types=1);

namespace App\Form;

use Override;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextSuffixType extends TextType {
    #[Override]
    public function configureOptions(OptionsResolver $resolver): void {
        parent::configureOptions($resolver);

        $resolver->setRequired('suffix');
    }

    #[Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->addModelTransformer(new CallbackTransformer(
                fn($username): string => rtrim((string) $username, $options['suffix']),
                fn($input): string => sprintf('%s%s', $input, $options['suffix'])
            ));
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void {
        parent::finishView($view, $form, $options);

        $view->vars['suffix'] = $options['suffix'];
    }

    #[Override]
    public function getBlockPrefix(): string {
        return 'text_suffix';
    }
}
