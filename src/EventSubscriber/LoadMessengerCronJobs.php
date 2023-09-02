<?php

namespace App\EventSubscriber;

use Shapecode\Bundle\CronBundle\Domain\CronJobMetadata;
use Shapecode\Bundle\CronBundle\Event\LoadJobsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Command\ConsumeMessagesCommand;

class LoadMessengerCronJobs implements EventSubscriberInterface {

    public function __construct(private readonly ConsumeMessagesCommand $command) {

    }

    public function onLoadJobs(LoadJobsEvent $event): void {
        $event->addJob(CronJobMetadata::createByCommand('*/1 * * * *', $this->command, 'async -vv --time-limit=20 --limit=25 --no-reset'));
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array {
        return [
            LoadJobsEvent::class => 'onLoadJobs',
        ];
    }
}