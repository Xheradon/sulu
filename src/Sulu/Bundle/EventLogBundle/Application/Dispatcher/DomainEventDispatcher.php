<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\EventLogBundle\Application\Dispatcher;

use Sulu\Bundle\EventLogBundle\Domain\Event\DomainEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class DomainEventDispatcher implements DomainEventDispatcherInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function dispatch(DomainEvent $event): DomainEvent
    {
        // dispatch event with DomainEvent::class as event-name to allow for listening to all domain events. the
        // DispatchSpecificDomainEventSubscriber will additionally dispatch the event with the specific event-name.
        /** @var DomainEvent $dispatchedEvent */
        $dispatchedEvent = $this->eventDispatcher->dispatch($event, DomainEvent::class);

        return $dispatchedEvent;
    }
}
