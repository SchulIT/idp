<?php

declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use App\Security\Session\HandleActiveSessionSubscriber;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RemoveHandleActiveSessionSubscriber implements CompilerPassInterface {

    public function process(ContainerBuilder $container): void {
        $container->removeDefinition(HandleActiveSessionSubscriber::class);
    }
}
