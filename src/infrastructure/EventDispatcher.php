<?php
/******************************************************************************
 * Copyright (c) 2016 Constantin Galbenu <gica.galbenu@gmail.com>             *
 ******************************************************************************/

namespace Cqrs;


class EventDispatcher
{
    /** @var EventSubscriber */
    private $eventSubscriber;

    private $dispatchedEvents = [];

    public function __construct(
        EventSubscriber $eventSubscriber
    )
    {
        $this->eventSubscriber = $eventSubscriber;
    }

    public function triggerEvent($event)
    {
        $this->dispatchedEvents[] = $event;

        $listeners = $this->eventSubscriber->getListenersForEvent($event);

        if (!$listeners) {
            return;
        }

        foreach ($listeners as $listener) {

            $returnValue = call_user_func($listener, $event);

            if (false === $returnValue) {
                break;
            }
        }
    }

    /**
     * @return array
     */
    public function getDispatchedEvents(): array
    {
        return $this->dispatchedEvents;
    }
}