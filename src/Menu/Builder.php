<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface {
    use ContainerAwareTrait;

    private $factory;

    public function __construct(FactoryInterface $factory) {
        $this->factory = $factory;
    }

    public function mainMenu(array $options) {
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

        $authorizationChecker = $this->container->get('security.authorization_checker');

        if($authorizationChecker->isGranted('ROLE_ADMIN')) {
            $menu->addChild('user_menu.label', [
                'attributes' => [
                    'class' => 'header'
                ]
            ]);

            $menu->addChild('users.label', [
                'route' => 'users'
            ]);
        }

        if($authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
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