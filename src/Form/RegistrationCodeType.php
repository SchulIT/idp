<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationCodeType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('code', CodeGeneratorType::class, [
                'label' => 'label.code'
            ])
            ->add('student', EntityType::class, [
                'label' => 'label.student',
                'class' => User::class,
                'choice_label' => function(User $user) {
                    if(!empty($user->getGrade())) {
                        return sprintf('%s, %s (%s)', $user->getLastname(), $user->getFirstname(), $user->getGrade());
                    }

                    return sprintf('%s, %s (%s)', $user->getLastname(), $user->getFirstname(), $user->getUsername());
                },
                'query_builder' => fn(EntityRepository $repository) => $repository->createQueryBuilder('u')
                    ->leftJoin('u.type', 't')
                    ->where("t.alias = 'student'")
                    ->orderBy('u.username', 'asc'),
                'placeholder' => 'label.select',
                'attr' => [
                    'data-choice' => 'true'
                ]
            ])
            ->add('validFrom', DateType::class, [
                'label' => 'label.enabled_from',
                'required' => false,
                'widget' => 'single_text'
            ]);
    }
}