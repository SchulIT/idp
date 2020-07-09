<?php

namespace App\Menu;

use App\Entity\ServiceProvider;
use App\Entity\User;
use App\Service\UserServiceProviderResolver;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use SchulIT\CommonBundle\DarkMode\DarkModeManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Builder {
    private $factory;
    private $authorizationChecker;
    private $translator;
    private $tokenStorage;
    private $userServiceProviderResolver;
    private $darkModeManager;

    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $authorizationChecker,
                                TranslatorInterface $translator, TokenStorageInterface $tokenStorage,
                                UserServiceProviderResolver $userServiceProviderResolver, DarkModeManagerInterface $darkModeManager) {
        $this->factory = $factory;
        $this->authorizationChecker = $authorizationChecker;
        $this->translator = $translator;
        $this->tokenStorage = $tokenStorage;
        $this->userServiceProviderResolver = $userServiceProviderResolver;
        $this->darkModeManager = $darkModeManager;
    }

    public function mainMenu(): ItemInterface {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttribute('class', 'navbar-nav mr-auto');

        $menu->addChild('dashboard.label', [
            'route' => 'dashboard'
        ])
            ->setAttribute('icon', 'fa fa-home');

        $menu->addChild('profile.label', [
            'route' => 'profile'
        ])
            ->setAttribute('icon', 'fas fa-user');

        $menu->addChild('two_factor.label', [
            'route' => 'two_factor'
        ])
            ->setAttribute('icon', 'fas fa-shield-alt');

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $menu->addChild('users.label', [
                'route' => 'users'
            ])
                ->setAttribute('icon', 'fas fa-users');

            $menu->addChild('codes.label', [
                'route' => 'registration_codes'
            ])
                ->setAttribute('icon', 'fas fa-qrcode');

            $menu->addChild('privacy_policy.label', [
                'route' => 'edit_privacy_policy'
            ])
                ->setAttribute('icon', 'fas fa-user-shield');
        } else {
            $menu->addChild('privacy_policy.label', [
                'route' => 'show_privacy_policy'
            ])
                ->setAttribute('icon', 'fas fa-user-shield');
        }

        return $menu;
    }

    public function adminMenu(array $options) {
        $root = $this->factory->createItem('root')
            ->setChildrenAttributes([
                'class' => 'navbar-nav float-lg-right'
            ]);

        $menu = $root->addChild('admin', [
            'label' => ''
        ])
            ->setAttribute('icon', 'fa fa-cogs')
            ->setAttribute('title', $this->translator->trans('management.label'))
            ->setExtra('menu', 'admin')
            ->setExtra('menu-container', '#submenu')
            ->setExtra('pull-right', true);

        if($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $menu->addChild('user_types.label', [
                'route' => 'user_types'
            ])
                ->setAttribute('icon', 'fas fa-user-cog');

            $menu->addChild('user_roles.label', [
                'route' => 'user_roles'
            ])
                ->setAttribute('icon', 'fas fa-user-tag');

            $menu->addChild('ad_sync_options.label', [
                'route' => 'ad_sync_options'
            ])
                ->setAttribute('icon', 'fas fa-sync');

            $menu->addChild('service_providers.label', [
                'route' => 'service_providers'
            ])
                ->setAttribute('icon', 'fa fa-th');

            $menu->addChild('service_attributes.label', [
                'route' => 'attributes'
            ])
                ->setAttribute('icon', 'far fa-list-alt');

            $menu->addChild('idp.details', [
                'route' => 'idp_details'
            ])
                ->setAttribute('icon', 'fas fa-info-circle');

            $menu->addChild('applications.label', [
                'route' => 'applications'
            ])
                ->setAttribute('icon', 'fas fa-key');

            $menu->addChild('cron.label', [
                'route' => 'admin_cronjobs'
            ])
                ->setAttribute('icon', 'fas fa-history');

            $menu->addChild('logs.label', [
                'route' => 'admin_logs'
            ])
                ->setAttribute('icon', 'fas fa-clipboard-list');

            $menu->addChild('mails.label', [
                'route' => 'admin_mails'
            ])
                ->setAttribute('icon', 'far fa-envelope');

            $menu->addChild('api.doc', [
                'uri' => '/docs/api'
            ]);
        }

        return $root;
    }

    public function userMenu(array $options): ItemInterface {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttributes([
                'class' => 'navbar-nav float-lg-right'
            ]);

        $user = $this->tokenStorage->getToken()->getUser();

        if(!$user instanceof User) {
            return $menu;
        }

        $label = 'dark_mode.enable';
        $icon = 'far fa-moon';

        if($this->darkModeManager->isDarkModeEnabled()) {
            $label = 'dark_mode.disable';
            $icon = 'far fa-sun';
        }

        $menu->addChild($label, [
            'route' => 'toggle_darkmode',
            'label' => ''
        ])
            ->setAttribute('icon', $icon)
            ->setAttribute('title', $this->translator->trans($label));

        $displayName = $user->getUsername();

        $menu->addChild('user', [
            'label' => $displayName
        ])
            ->setAttribute('icon', 'fa fa-user');

        $menu->addChild('label.logout', [
            'route' => 'logout',
            'label' => ''
        ])
            ->setAttribute('icon', 'fas fa-sign-out-alt')
            ->setAttribute('title', $this->translator->trans('auth.logout'));

        return $menu;
    }

    public function servicesMenu(): ItemInterface {
        $root = $this->factory->createItem('root')
            ->setChildrenAttributes([
                'class' => 'navbar-nav float-lg-right'
            ]);

        $token = $this->tokenStorage->getToken();
        $user = $token->getUser();

        if($user instanceof User) {
            $menu = $root->addChild('services', [
                'label' => ''
            ])
                ->setAttribute('icon', 'fa fa-th')
                ->setExtra('menu', 'services')
                ->setExtra('pull-right', true)
                ->setAttribute('title', $this->translator->trans('services.label'));

            $services = $this->userServiceProviderResolver->getServices($user);
            foreach ($services as $service) {
                $menu->addChild($service->getName(), [
                    'uri' => $service->getUrl()
                ])
                    ->setAttribute('title', $service->getDescription())
                    ->setAttribute('target', '_blank');
            }
        }

        return $root;
    }
}