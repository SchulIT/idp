<?php

namespace App\Form;

use App\Entity\RegistrationCode;
use App\Entity\User;
use App\Entity\UserType as UserTypeEntity;
use App\Service\AttributeResolver;
use Doctrine\ORM\EntityRepository;
use SchulIT\CommonBundle\Form\FieldsetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class RegistrationCodeType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
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
                'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('u')
                        ->leftJoin('u.type', 't')
                        ->where("t.alias = 'student'")
                        ->orderBy('u.username', 'asc');
                },
                'placeholder' => 'label.select',
                'attr' => [
                    'data-choice' => 'true'
                ]
            ]);
    }
}