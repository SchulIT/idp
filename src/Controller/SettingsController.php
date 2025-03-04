<?php

namespace App\Controller;

use App\Settings\AppSettings;
use App\Settings\LoginSettings;
use App\Settings\RegistrationSettings;
use Jbtronics\SettingsBundle\Form\SettingsFormFactoryInterface;
use Jbtronics\SettingsBundle\Manager\SettingsManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/admin/settings')]
class SettingsController extends AbstractController {

    #[Route(path: '', name: 'settings')]
    public function app(Request $request, SettingsManagerInterface $settingsManager, SettingsFormFactoryInterface $formFactory): Response {
        $settings = [
            $settingsManager->createTemporaryCopy(AppSettings::class),
            $settingsManager->createTemporaryCopy(LoginSettings::class),
            $settingsManager->createTemporaryCopy(RegistrationSettings::class)
            ];

        $form = $formFactory->createMultiSettingsFormBuilder($settings)->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            foreach($settings as $setting) {
                $settingsManager->mergeTemporaryCopy($setting);
            }

            $settingsManager->save();

            $this->addFlash('success', 'settings.success');

            return $this->redirectToRoute('settings');
        }

        return $this->render('settings/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}