<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Builder {
    private $factory;
    private $authorizationChecker;

    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $authorizationChecker) {
        $this->factory = $factory;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function mainMenu() {
        $menu = $this->factory->createItem('root')
            ->setChildrenAttribute('class', 'nav nav-pills flex-column');

        $menu->addChild('menu.label', [
            'attributes' => [
                'class' => 'header'
            ]
        ]);

        $menu->addChild('dashboard.label', [
            'route' => 'dashboard'
        ]);

        $menu->addChild('profile.label', [
            'route' => 'profile'
        ]);

        $menu->addChild('two_factor.label', [
            'route' => 'two_factor'
        ]);

        if($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $menu->addChild('user_menu.label', [
                'attributes' => [
                    'class' => 'header'
                ]
            ]);

            $menu->addChild('users.label', [
                'route' => 'users'
            ]);
        }

        if($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $menu->addChild('management.label', [
                'attributes' => [
                    'class' => 'header'
                ]
            ]);

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
        }

        return $menu;
    }
}