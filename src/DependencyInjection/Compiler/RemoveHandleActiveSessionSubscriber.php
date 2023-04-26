<?php

namespace App\DependencyInjection\Compiler;

use App\Security\Session\HandleActiveSessionSubscriber;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RemoveHandleActiveSessionSubscriber implements CompilerPassInterface {

    public function process(ContainerBuilder $container) {
        $container->removeDefinition(HandleActiveSessionSubscriber::class);
    }
}