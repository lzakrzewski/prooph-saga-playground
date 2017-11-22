<?php

declare(strict_types=1);

namespace tests;

use Console\Middleware\CollectsMessages;
use Messaging\DomainEvent;
use PHPUnit\Framework\TestCase;
use Prooph\ServiceBus\CommandBus;

class Scenario
{
    /** @var CommandBus */
    private $commandBus;

    /** @var CollectsMessages */
    private $collectedMessages;

    /** @var TestCase */
    private $testCase;

    public function __construct(CommandBus $commandBus, CollectsMessages $messages, TestCase $testCase)
    {
        $this->commandBus          = $commandBus;
        $this->testCase            = $testCase;
        $this->collectedMessages   = $messages;
    }

    public function when($command)
    {
        $this->commandBus->dispatch($command);

        return $this;
    }

    public function then(...$events)
    {
        $collectedEvents = array_filter(
            $this->collectedMessages->all(),
            function ($message) {
                return $message instanceof DomainEvent;
            }
        );

        $this->assertEvents($events, $collectedEvents);
    }

    private function assertEvents(array $expectedEvents, array $recordedEvents)
    {
        $this->testCase->assertCount(
            $expectedCount = count($expectedEvents),
            $recordedEvents,
            sprintf('Expected %d events to be recorded, but %d recorded', $expectedCount, count($recordedEvents))
        );

        foreach ($expectedEvents as $key => $event) {
            $recordedEvent = $recordedEvents[$key];

            $this->testCase->assertInstanceOf(get_class($expectedEvents[$key]), $recordedEvents[$key]);
            $this->testCase->assertEquals($event->payload(), $recordedEvent->payload());
        }
    }
}
