<?php

declare(strict_types=1);

/* Copyright (c) 2004-2013 Bart van den Burg

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is furnished
to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE. */
namespace App\Form;

use App\Form\DataTransformer\HashToKeyValueArrayTransformer;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class KeyValueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder->addModelTransformer(new HashToKeyValueArrayTransformer($options['use_container_object']));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $e): void {
            $input = $e->getData();

            if (null === $input) {
                return;
            }

            $output = [];

            foreach ($input as $key => $value) {
                $output[] = ['key' => $key, 'value' => $value];
            }

            $e->setData($output);
        }, 1);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults(['entry_type' => KeyValueRowType::class, 'allow_add' => true, 'allow_delete' => true, 'key_type' => TextType::class, 'key_options' => [], 'value_options' => [], 'allowed_keys' => null, 'use_container_object' => false, 'entry_options' => fn(Options $options): array => ['key_type' => $options['key_type'], 'value_type' => $options['value_type'], 'key_options' => $options['key_options'], 'value_options' => $options['value_options'], 'allowed_keys' => $options['allowed_keys']]]);

        $resolver->setRequired(['value_type']);
        $resolver->setAllowedTypes('allowed_keys', ['null', 'array']);
    }

    #[Override]
    public function getParent(): string {
        return CollectionType::class;
    }

    public function getName(): string {
        return $this->getBlockPrefix();
    }

    #[Override]
    public function getBlockPrefix(): string {
        return 'burgov_key_value';
    }
}
