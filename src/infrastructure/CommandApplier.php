<?php


namespace Cqrs;


class CommandApplier
{
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    public function __construct(\Cqrs\EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function applyCommand($aggregate, $command)
    {
        $methodName = $this->getMethodName($command);

        $generator = call_user_func([$aggregate, $methodName], $command);

        $events = [];

        foreach ($generator as $event) {
            $events[] = $event;
        }

        foreach ($events as $event) {
            $this->eventDispatcher->triggerEvent($event);
        }
    }

    private function getMethodName($event)
    {
        $parts = explode('\\', get_class($event));

        return 'handle' . end($parts);
    }
}