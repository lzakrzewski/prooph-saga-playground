<?php

declare(strict_types=1);

namespace tests;

use PHPUnit\Framework\TestCase;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\StreamName;
use Prooph\ServiceBus\CommandBus;

class Scenario
{
    /** @var CommandBus */
    private $commandBus;

    /** @var EventStore */
    private $eventStore;

    /** @var TestCase */
    private $testCase;

    public function __construct(CommandBus $commandBus, EventStore $eventStore, TestCase $testCase)
    {
        $this->commandBus = $commandBus;
        $this->testCase   = $testCase;
        $this->eventStore = $eventStore;
    }

    public function when($command)
    {
        $this->commandBus->dispatch($command);

        return $this;
    }

    public function then(...$events)
    {
        $recordedEvents = $this->recordedEvents();

        $this->assertEvents($events, $recordedEvents);
    }

    private function recordedEvents(): array
    {
        return array_reduce(
            $this->eventStore->fetchStreamNames(null, null),
            function (array $events, StreamName $streamName) {
                return array_merge($events, iterator_to_array($this->eventStore->load($streamName)));
            },
            []
        );
    }

    private function assertEvents(array $expectedEvents, array $recordedEvents)
    {
        foreach ($expectedEvents as $key => $event) {
            $recordedEvent = $recordedEvents[$key];

            $this->testCase->assertInstanceOf(get_class($expectedEvents[$key]), $recordedEvents[$key]);
            $this->testCase->assertEquals($event->payload(), $recordedEvent->payload());
        }
    }
}
