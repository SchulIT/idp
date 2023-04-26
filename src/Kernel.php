<?php

namespace App;

use App\DependencyInjection\Compiler\RemoveHandleActiveSessionSubscriber;
use App\DependencyInjection\Compiler\RemovePcntlEventSubscriberPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container) {
        $container->addCompilerPass(new RemovePcntlEventSubscriberPass());

        if($this->environment === 'test') {
            $container->addCompilerPass(new RemoveHandleActiveSessionSubscriber());
        }
    }
}
