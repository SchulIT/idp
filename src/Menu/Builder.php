<?php

namespace App\Menu;

use App\Entity\ServiceProvider;
use App\Entity\User;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Builder {
    private $factory;
    private $authorizationChecker;
    private $translator;
    private $tokenStorage;

    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $authorizationChecker, TranslatorInterface $translator, TokenStorageInterface $tokenStorage) {
        $this->factory = $factory;
        $this->authorizationChecker = $authorizationChecker;
        $this->translator = $translator;
        $this->tokenStorage = $tokenStorage;
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
            ]);

            $menu->addChild('user_roles.label', [
                'route' => 'user_roles'
            ]);

            $menu->addChild('ad_sync_options.label', [
                'route' => 'ad_sync_options'
            ]);

            $menu->addChild('service_providers.label', [
                'route' => 'service_providers'
            ]);

            $menu->addChild('service_attributes.label', [
                'route' => 'attributes'
            ]);

            $menu->addChild('idp.details', [
                'route' => 'idp_details'
            ]);

            $menu->addChild('applications.label', [
                'route' => 'applications'
            ]);

            $menu->addChild('logs.label', [
                'route' => 'admin_logs'
            ]);

            $menu->addChild('mails.label', [
                'route' => 'admin_mails'
            ]);

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

        if($user === null || !$user instanceof User) {
            return $menu;
        }

        $displayName = $user->getUsername();

        $userMenu = $menu->addChild('user', [
            'label' => $displayName
        ])
            ->setAttribute('icon', 'fa fa-user')
            ->setExtra('menu', 'user')
            ->setExtra('menu-container', '#submenu')
            ->setExtra('pull-right', true);

        $userMenu->addChild('profile.label', [
            'route' => 'profile'
        ]);

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

            /** @var ServiceProvider $service */
            foreach ($user->getEnabledServices() as $service) {
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