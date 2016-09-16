<?php


namespace Cqrs;


class EventSubscriber
{
    private $eventListeners = [];

    public function subscribeToEvent($listener)
    {
        $this->eventListeners[] = $listener;
    }

    /**
     * @param $event
     * @return callable[]
     */
    public function getListenersForEvent($event)
    {
        $result = [];

        foreach ($this->eventListeners as $eventListener) {
            $methodName = $this->getMethodName($event);

            $method = [$eventListener, $methodName];

            if (is_callable($method)) {
                $result[] = $method;
            }
        }

        return $result;
    }

    private function getMethodName($event)
    {
        $parts = explode('\\', get_class($event));

        return 'handle' . end($parts);
    }

}