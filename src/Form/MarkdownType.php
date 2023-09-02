<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MarkdownType extends TextareaType {

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver
            ->setDefault('upload_enabled', true)
            ->setDefault('upload_url', null)
            ->setDefault('preview_url', $this->urlGenerator->generate('markdown_preview', [], UrlGeneratorInterface::ABSOLUTE_PATH))
            ->setDefault('required', false)
            ->setDefault('enable_links', true);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void {
        parent::buildView($view, $form, $options);

        $view->vars['attr'] = [
            'data-editor' => 'markdown',
            'data-language' => 'de',
            'data-url' => $options['upload_url'],
            'data-preview' => $options['preview_url']
        ];
    }

    public function getBlockPrefix(): string {
        return 'markdown';
    }
}