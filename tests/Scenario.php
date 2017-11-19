<?php

declare(strict_types=1);

namespace tests;

use PHPUnit\Framework\TestCase;
use Prooph\EventStore\EventStore;
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
        $recordedEvents = [];

        foreach ($this->eventStore->fetchStreamNames(null, null) as $streamName) {
            foreach ($this->eventStore->load($streamName) as $event) {
                $recordedEvents[] = $event;
            }
        }

        foreach ($events as $key => $event) {
            $recordedEvent = $recordedEvents[$key];

            $this->testCase->assertInstanceOf(get_class($events[$key]), $recordedEvents[$key]);
            $this->testCase->assertEquals($event->payload(), $recordedEvent->payload());
        }
    }
}
