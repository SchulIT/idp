<?php

namespace App\Controller;

use App\Form\MarkdownType;
use App\Form\TextPrefixType;
use App\Settings\AppSettings;
use App\Settings\LoginSettings;
use App\Settings\RegistrationSettings;
use App\Settings\SettingsManager;
use SchulIT\CommonBundle\Form\FieldsetType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Route(path: '/admin/settings')]
class SettingsController extends AbstractController {

    #[Route(path: '', name: 'settings')]
    public function index(Request $request, SettingsManager $settingsManager, AppSettings $appSettings,
                          LoginSettings $loginSettings, RegistrationSettings $registrationSettings): Response {
        $settings = [
            'helpdesk:mail' => $appSettings->getHelpdeskMail(),
            'security:password_compromised_check' => $appSettings->isPasswordCompromisedCheckEnabled(),
            'login:message' => $loginSettings->getLoginMessage(),
            'registration:suffix' => $registrationSettings->getUsernameSuffix(),
        ];

        $form = $this->createFormBuilder($settings)
            ->add('group_app', FieldsetType::class, [
                'legend' => 'settings.general.label',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('helpdesk:mail', EmailType::class, [
                            'required' => false,
                            'constraints' => [
                                new NotBlank(['allowNull' => true]),
                                new Email()
                            ],
                            'label' => 'settings.helpdesk.mail.label',
                            'help' => 'settings.helpdesk.mail.help'
                        ])
                        ->add('security:password_compromised_check', CheckboxType::class, [
                            'required' => false,
                            'label' => 'settings.security.password_compromised_check.label',
                            'help' => 'settings.security.password_compromised_check.help',
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ]);
                }
            ])
            ->add('group_login', FieldsetType::class, [
                'legend' => 'settings.login.label',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('login:message', MarkdownType::class, [
                            'required' => false,
                            'label' => 'settings.login.message.label',
                            'help' => 'settings.login.message.help'
                        ]);
                }
            ])
            ->add('group_registration', FieldsetType::class, [
                'legend' => 'settings.registration.label',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('registration:suffix', TextPrefixType::class, [
                            'required' => true,
                            'label' => 'settings.registration.suffix.label',
                            'help' => 'settings.registration.suffix.help',
                            'prefix' => '@',
                            'constraints' => [
                                new NotBlank()
                            ]
                        ]);
                }
            ])
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $settings = $form->getData();

            foreach($settings as $key => $value) {
                $key = str_replace(':', '.', $key);
                $settingsManager->setValue($key, $value);
            }

            $this->addFlash('success', 'settings.success');

            return $this->redirectToRoute('settings');
        }

        return $this->render('settings/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}