<?php

declare(strict_types=1);

namespace App\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Messenger\EventListener\DispatchPcntlSignalListener;

class RemovePcntlEventSubscriberPass implements CompilerPassInterface {

    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container): void {
        $container->removeDefinition(DispatchPcntlSignalListener::class);
        $container->removeDefinition('messenger.listener.dispatch_pcntl_signal_listener');
        $container->removeDefinition('messenger.listener.stop_worker_on_sigterm_signal_listener');
    }
}
