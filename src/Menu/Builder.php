<?php

namespace App\Menu;

use App\Entity\ServiceProvider;
use App\Entity\User;
use App\Security\Voter\ProfileVoter;
use App\Service\UserServiceProviderResolver;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use ProxyManager\Proxy\ValueHolderInterface;
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
    private $adAuthEnabled;

    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $authorizationChecker,
                                TranslatorInterface $translator, TokenStorageInterface $tokenStorage,
                                UserServiceProviderResolver $userServiceProviderResolver, DarkModeManagerInterface $darkModeManager,
                                bool $adAuthEnabled) {
        $this->factory = $factory;
        $this->authorizationChecker = $authorizationChecker;
        $this->translator = $translator;
        $this->tokenStorage = $tokenStorage;
        $this->userServiceProviderResolver = $userServiceProviderResolver;
        $this->darkModeManager = $darkModeManager;
        $this->adAuthEnabled = $adAuthEnabled;
    }

    public function mainMenu(): ItemInterface {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttribute('class', 'navbar-nav mr-auto');

        $menu->addChild('dashboard.label', [
            'route' => 'dashboard'
        ])
            ->setExtra('icon', 'fa fa-home');

        $menu->addChild('profile.label', [
            'route' => 'profile'
        ])
            ->setExtra('icon', 'fas fa-user');

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $menu->addChild('users.label', [
                'route' => 'users'
            ])
                ->setExtra('icon', 'fas fa-users');

            $menu->addChild('codes.label', [
                'route' => 'registration_codes'
            ])
                ->setExtra('icon', 'fas fa-qrcode');

            $menu->addChild('privacy_policy.label', [
                'route' => 'edit_privacy_policy'
            ])
                ->setExtra('icon', 'fas fa-user-shield');
        } else {
            $menu->addChild('privacy_policy.label', [
                'route' => 'show_privacy_policy'
            ])
                ->setExtra('icon', 'fas fa-user-shield');
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
            ->setExtra('icon', 'fa fa-cogs')
            ->setAttribute('title', $this->translator->trans('management.label'))
            ->setExtra('menu', 'admin')
            ->setExtra('menu-container', '#submenu')
            ->setExtra('pull-right', true);

        if($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $menu->addChild('settings.label', [
                'route' => 'settings'
            ])
                ->setExtra('icon', 'fas fa-wrench');

            $menu->addChild('user_types.label', [
                'route' => 'user_types'
            ])
                ->setExtra('icon', 'fas fa-user-cog');

            $menu->addChild('user_roles.label', [
                'route' => 'user_roles'
            ])
                ->setExtra('icon', 'fas fa-user-tag');

            if($this->adAuthEnabled === true) {
                $menu->addChild('ad_sync_options.label', [
                    'route' => 'ad_sync_options'
                ])
                    ->setExtra('icon', 'fas fa-sync');
            }

            $menu->addChild('service_providers.label', [
                'route' => 'service_providers'
            ])
                ->setExtra('icon', 'fa fa-th');

            $menu->addChild('service_attributes.label', [
                'route' => 'attributes'
            ])
                ->setExtra('icon', 'far fa-list-alt');

            $menu->addChild('idp.details', [
                'route' => 'idp_details'
            ])
                ->setExtra('icon', 'fas fa-info-circle');

            $menu->addChild('applications.label', [
                'route' => 'applications'
            ])
                ->setExtra('icon', 'fas fa-key');

            $menu->addChild('cron.label', [
                'route' => 'admin_cronjobs'
            ])
                ->setExtra('icon', 'fas fa-history');

            $menu->addChild('logs.label', [
                'route' => 'admin_logs'
            ])
                ->setExtra('icon', 'fas fa-clipboard-list');

            $menu->addChild('messenger.label', [
                'route' => 'admin_messenger'
            ])
                ->setExtra('icon', 'fas fa-envelope-open-text');

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
            ->setExtra('icon', $icon)
            ->setAttribute('title', $this->translator->trans($label));

        $displayName = $user->getUsername();

        $menu->addChild('user', [
            'label' => $displayName
        ])
            ->setExtra('icon', 'fa fa-user');

        $menu->addChild('label.logout', [
            'route' => 'logout',
            'label' => ''
        ])
            ->setExtra('icon', 'fas fa-sign-out-alt')
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
                ->setExtra('icon', 'fa fa-th')
                ->setExtra('menu', 'services')
                ->setExtra('pull-right', true)
                ->setAttribute('title', $this->translator->trans('services.label'));

            $services = $this->userServiceProviderResolver->getServices($user);
            foreach ($services as $service) {
                $item = $menu->addChild($service->getName(), [
                    'uri' => $service->getUrl()
                ])
                    ->setAttribute('title', $service->getDescription())
                    ->setLinkAttribute('target', '_blank');

                if(!empty($service->getIcon())) {
                    $item->setExtra('icon', $service->getIcon());
                }
            }
        }

        return $root;
    }
}