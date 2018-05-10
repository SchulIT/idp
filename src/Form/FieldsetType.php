<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldsetType extends AbstractType {
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'legend' => '',
            'inherit_data' => true,
            'options' => [ ],
            'fields' => [ ],
            'label' => false
        ])
            ->addAllowedTypes('fields', ['array', 'callable']);
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        if(!empty($options['fields'])) {
            if(is_callable($options['fields'])) {
                $options['fields']($builder);
            } else if(is_array($options['fields'])) {
                foreach($options['fields'] as $field) {
                    $builder->add($field['name'], $field['type'], $field['attr']);
                }
            }
        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options) {
        if($options['legend'] !== false) {
            $view->vars['legend'] = $options['legend'];
        }
    }

    public function getName() {
        return 'fieldset';
    }
}