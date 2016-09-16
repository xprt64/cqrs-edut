<?php


namespace Cqrs;


class EventsApplier
{

    public function applyEvents($aggregate, array $priorEvents = [])
    {
        foreach ($priorEvents as $event) {
            $this->applyEvent($aggregate, $event);
        }
    }

    private function applyEvent($aggregate, $event)
    {
        $methodName = $this->getMethodName($event);

        call_user_func([$aggregate, $methodName], $event);
    }

    private function getMethodName($event)
    {
        $parts = explode('\\', get_class($event));

        return 'apply' . end($parts);
    }
}