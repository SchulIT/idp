<?php

namespace App\EventSubscriber;

use GpsLab\Bundle\GeoIP2Bundle\Command\UpdateDatabaseCommand;
use Shapecode\Bundle\CronBundle\Domain\CronJobMetadata;
use Shapecode\Bundle\CronBundle\Event\LoadJobsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Command\ConsumeMessagesCommand;

class LoadMessengerCronJobs implements EventSubscriberInterface {

    public function __construct(private readonly ConsumeMessagesCommand $command, private readonly UpdateDatabaseCommand $updateDatabaseCommand) {

    }

    public function onLoadJobs(LoadJobsEvent $event): void {
        $event->addJob(CronJobMetadata::createByCommand('*/1 * * * *', $this->command, 'async -vv --time-limit=20 --limit=25 --no-reset'));
        $event->addJob(CronJobMetadata::createByCommand('@daily', $this->updateDatabaseCommand));
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