<?php

declare(strict_types=1);

namespace tests;

use Infrastructure\Middleware\CollectsMessages;
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
        $this->commandBus        = $this->setupCommandBus($commandBus);
        $this->eventBus          = $eventBus;
        $this->testCase          = $testCase;
        $this->collectedMessages = $messages;
    }

    public function given(...$events): self
    {
        $this->commandBus->willNotDispatchCommands();

        foreach ($events as $event) {
            $this->eventBus->dispatch($event);
        }

        $this->commandBus->willDispatchCommands();

        return $this;
    }

    public function when(Message $message): self
    {
        $this->dispatch($message);

        return $this;
    }

    public function then(...$expectedMessages): self
    {
        $collectedMessages = $this->collectedMessages->all();

        foreach ($expectedMessages as $expectedMessage) {
            $this->assertThatMessageWasCollected($expectedMessage, $collectedMessages);
        }

        return $this;
    }

    public function thenNot(...$notExpectedMessages): self
    {
        $collectedMessages = $this->collectedMessages->all();

        foreach ($notExpectedMessages as $notExpectedMessage) {
            $this->assertThatMessageWasNotCollected($notExpectedMessage, $collectedMessages);
        }

        return $this;
    }

    public function and(...$expectedMessages): self
    {
        return $this->then($expectedMessages);
    }

    public function but(...$expectedMessages): self
    {
        return call_user_func_array([$this, 'then'], $expectedMessages);
    }

    private function assertThatMessageWasCollected(Message $expectedMessage, array $collectedMessages)
    {
        $collectedMessage = $this->findMessage($expectedMessage, $collectedMessages);

        if (null === $collectedMessage && null !== $messageWithSameClass =$this->findMessageWithClass($expectedMessage, $collectedMessages)) {
            $this->testCase->fail(
                sprintf(
                    'Expected that message with class "%s" and aggregateId "%s", found message with same class but different aggregate id "%s"',
                    get_class($expectedMessage),
                    $expectedMessage->aggregateId(),
                    $messageWithSameClass->aggregateId()
                )
            );
        }

        if (null === $collectedMessage) {
            $this->testCase->fail(
                sprintf(
                    'Expected that message with class "%s" and aggregateId "%s", to be collected.',
                    get_class($expectedMessage),
                    $expectedMessage->aggregateId()
                )
            );
        }

        $this->testCase->assertEquals($expectedMessage->aggregateId(), $collectedMessage->aggregateId());
        $this->testCase->assertEquals($expectedMessage->payload(), $collectedMessage->payload());
    }

    private function assertThatMessageWasNotCollected(Message $notExpectedMessage, array $collectedMessages)
    {
        $this->testCase->assertNull(
            $this->findMessage($notExpectedMessage, $collectedMessages),
            sprintf(
                'Expected that message with class "%s" and aggregateId "%s", will not be collected.',
                get_class($notExpectedMessage),
                $notExpectedMessage->aggregateId()
            )
        );
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

    private function findMessage(Message $expectedMessage, array $collectedMessages): ?Message
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

        return null;
    }

    private function findMessageWithClass(Message $expectedMessage, array $collectedMessages): ?Message
    {
        foreach ($collectedMessages as $collectedMessage) {
            if (true === $collectedMessage instanceof $expectedMessage) {
                return $collectedMessage;
            }
        }

        return null;
    }

    private function setupCommandBus($commandBus): CommandBus
    {
        return new class($commandBus) extends CommandBus {
            /** @var CommandBus */
            private $baseCommandBus;

            /** @var bool */
            private $willDispatchCommands = true;

            public function __construct(CommandBus $commandBus)
            {
                parent::__construct();

                $this->baseCommandBus = $commandBus;
            }

            public function willNotDispatchCommands()
            {
                $this->willDispatchCommands = false;
            }

            public function willDispatchCommands()
            {
                $this->willDispatchCommands = true;
            }

            public function dispatch($command): void
            {
                if (false === $this->willDispatchCommands) {
                    return;
                }

                $this->baseCommandBus->dispatch($command);
            }
        };
    }
}
