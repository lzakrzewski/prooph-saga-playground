<?php

declare(strict_types=1);

namespace tests;

use Console\Middleware\CollectsMessages;
use Messaging\Command;
use Messaging\DomainEvent;
use Messaging\Message;
use PHPUnit\Framework\TestCase;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\EventBus;

class Scenario
{
    /** @var CommandBus */
    private $commandBus;

    /** @var EventBus */
    private $eventBus;

    /** @var CollectsMessages */
    private $collectedMessages;

    /** @var TestCase */
    private $testCase;

    public function __construct(
        CommandBus $commandBus,
        EventBus $eventBus,
        CollectsMessages $messages,
        TestCase $testCase
    ) {
        $this->commandBus        = $commandBus;
        $this->eventBus          = $eventBus;
        $this->testCase          = $testCase;
        $this->collectedMessages = $messages;
    }

    public function given(...$events)
    {
        foreach ($events as $event) {
            $this->eventBus->dispatch($event);
        }

        return $this;
    }

    public function when(Message $message)
    {
        $this->dispatch($message);

        return $this;
    }

    public function then(...$expectedMessages)
    {
        $collectedMessages = $this->collectedMessages->all();

        foreach ($expectedMessages as $expectedMessage) {
            $this->assertMessage($expectedMessage, $collectedMessages);
        }
    }

    private function assertMessage(Message $expectedMessage, array $collectedMessages)
    {
        $collectedMessage = $this->findMessage($expectedMessage, $collectedMessages);

        $this->testCase->assertEquals($expectedMessage->aggregateId(), $collectedMessage->aggregateId());
        $this->testCase->assertEquals($expectedMessage->payload(), $collectedMessage->payload());
    }

    private function dispatch(Message $message)
    {
        if ($message instanceof Command) {
            $this->commandBus->dispatch($message);
        }

        if ($message instanceof DomainEvent) {
            $this->eventBus->dispatch($message);
        }
    }

    private function findMessage(Message $expectedMessage, array $collectedMessages): Message
    {
        foreach ($collectedMessages as $collectedMessage) {
            if (false === $collectedMessage instanceof $expectedMessage) {
                continue;
            }

            if (false === $collectedMessage->aggregateId()->equals($expectedMessage->aggregateId())) {
                continue;
            }

            return $collectedMessage;
        }

        $this->testCase->fail(
            sprintf(
                'Expected message with class "%s" and aggregateId "%s", to be collected.',
                get_class($expectedMessage),
                $expectedMessage->aggregateId()
            )
        );
    }
}
