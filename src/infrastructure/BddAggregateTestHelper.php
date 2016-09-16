<?php


namespace Cqrs;


class BddAggregateTestHelper extends \PHPUnit_Framework_TestCase
{
    /** @var \Cqrs\EventDispatcher */
    private $dispatcher;

    private $priorEvents = [];

    private $command;
    private $aggregate;
    private $aggregateHandlerMethodName;

    /** @var EventsApplier */
    private $eventsApplier;

    /** @var \Cqrs\CommandApplier */
    private $commandApplier;

    protected function setUp()
    {
        $subscriber = new \Cqrs\EventSubscriber();
        $this->dispatcher = new \Cqrs\EventDispatcher($subscriber);
        $this->eventsApplier = new \Cqrs\EventsApplier();
        $this->commandApplier = new \Cqrs\CommandApplier($this->dispatcher);

        $this->priorEvents = [];
        $this->command = null;
    }

    protected function onAggregate($aggregate)
    {
        $this->aggregate = $aggregate;
    }

    protected function given(...$priorEvents)
    {
        $this->priorEvents = $priorEvents;

    }

    protected function when($command)
    {
        $this->command = $command;
    }

    protected function then(...$expectedEvents)
    {
        $this->eventsApplier->applyEvents($this->aggregate, $this->priorEvents);

        $stateBeforeCommand = $this->hashObject($this->aggregate);

        $this->commandApplier->applyCommand($this->aggregate, $this->command);

        $this->assertEquals(
            $stateBeforeCommand,
            $this->hashObject($this->aggregate),
            sprintf("Command handler %s changed aggregate state!", get_class($this->command)));

        $this->assertTheseEvents($expectedEvents, $this->dispatcher->getDispatchedEvents());
    }

    private function hashObject($object)
    {
        ob_start();
        var_dump($object);
        return ob_get_clean();
    }

    protected function thenShouldFailWith($exceptionClass)
    {
        $this->expectException($exceptionClass);

        $this->eventsApplier->applyEvents($this->aggregate, $this->priorEvents);

        $this->commandApplier->applyCommand($this->aggregate, $this->command);
    }

    protected function assertTheseEvents(array $expectedEvents, array $actualEvents)
    {
        $expectedCount = count($expectedEvents);
        $actualCount = count($actualEvents);

        $this->assertEquals($expectedCount, $actualCount, sprintf("%d number of events were expected but %d number of events were generated", $expectedCount, $actualCount));
        $this->assertEquals($this->hashEvents($expectedEvents), $this->hashEvents($actualEvents), "Wrong events emitted");
    }

    protected function hashEvents(array $events)
    {
        return array_map([$this, 'hashEvent'], $events);
    }

    protected function hashEvent($event)
    {
        if (null === $event) {
            $this->fail("No event emitted!");
        }

        return array_merge(['___class' => get_class($event)], (array)($event));
    }

    /**
     * @return EventDispatcher
     */
    public function getDispatcher(): EventDispatcher
    {
        return $this->dispatcher;
    }


}