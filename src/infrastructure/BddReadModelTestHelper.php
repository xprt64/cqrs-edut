<?php


namespace Cqrs;


class BddReadModelTestHelper extends \PHPUnit_Framework_TestCase
{
    /** @var \Cqrs\EventSubscriber */
    private $subscriber;
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
        $this->subscriber = new \Cqrs\EventSubscriber();
        $this->dispatcher = new \Cqrs\EventDispatcher($this->subscriber);
        $this->eventsApplier = new \Cqrs\EventsApplier();
        $this->commandApplier = new \Cqrs\CommandApplier($this->dispatcher);

        $this->priorEvents = [];
        $this->command = null;
    }

    protected function subscribe($listener)
    {
        $this->subscriber->subscribeToEvent($listener);
    }

    protected function onAggregate($aggregate)
    {
        $this->aggregate = $aggregate;
    }

    protected function given(...$priorEvents)
    {
        foreach ($priorEvents as $event) {
            $this->dispatcher->triggerEvent($event);
        }
    }

    protected function assertTheseEvents(array $expectedEvents, array $actualEvents)
    {
        $expectedCount = count($expectedEvents);
        $actualCount = count($actualEvents);

        $this->assertEquals($expectedCount, $actualCount, sprintf("%d number of events were expected but %d number of events were generated", $expectedCount, $actualCount));
    }

    protected function hashObjects(array $events)
    {
        return array_map([$this, 'hashObject'], $events);
    }

    protected function hashObject($object)
    {
        if (null === $object) {
            $this->fail("No object to hash!");
        }

        return array_merge(['___class' => get_class($object)], (array)($object));
    }

    /**
     * @return EventDispatcher
     */
    public function getDispatcher(): EventDispatcher
    {
        return $this->dispatcher;
    }

    protected function assertEqualObjectsArray(array $expected, array $actual, $message = '')
    {
        $this->assertEquals(count($expected), count($actual), $message);
        $this->assertEquals($this->hashObjects($expected), $this->hashObjects($actual), $message);
    }


}