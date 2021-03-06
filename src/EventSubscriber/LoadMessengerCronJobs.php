<?php

namespace App\EventSubscriber;

use Shapecode\Bundle\CronBundle\Event\LoadJobsEvent;
use Shapecode\Bundle\CronBundle\Model\CronJobMetadata;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoadMessengerCronJobs implements EventSubscriberInterface {

    public function onLoadJobs(LoadJobsEvent $event) {
        $event->addJob(new CronJobMetadata('*/1 * * * *', 'messenger:consume', 'mail -vv --time-limit=20 --limit=25'));
        $event->addJob(new CronJobMetadata('*/1 * * * *', 'messenger:consume', 'provision -vv --time-limit=20 --limit=25'));
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents() {
        return [
            LoadJobsEvent::NAME => 'onLoadJobs',
        ];
    }
}