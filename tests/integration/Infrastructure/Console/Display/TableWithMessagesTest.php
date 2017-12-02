<?php

declare(strict_types=1);

namespace tests\integration\Infrastructure\Console\Display;

use Infrastructure\Console\Display\TableWithMessages;
use Infrastructure\Listener\MessageCollector;
use Messaging\Command\PlaceOrder;
use Messaging\Event\OrderConfirmed;
use Messaging\Event\OrderPlaced;
use Prooph\Common\Event\ActionEvent;
use Prophecy\Argument;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Output\BufferedOutput;
use tests\ContainerTestCase;

class TableWithMessagesTest extends ContainerTestCase
{
    /** @var MessageCollector */
    private $collectsMessages;

    /** @var TableWithMessages */
    private $tableWithMessages;

    /** @test */
    public function it_can_display_table_with_command_message(): void
    {
        $this->givenMessagesWereCollected(new PlaceOrder(Uuid::uuid4(), 5));

        $output = $this->display();

        $this->assertContains('Command', $output);
        $this->assertContains('PlaceOrder', $output);
    }

    /** @test */
    public function it_can_display_table_with_domain_event_message(): void
    {
        $this->givenMessagesWereCollected(new OrderPlaced(Uuid::uuid4(), 5));

        $output = $this->display();

        $this->assertContains('DomainEvent', $output);
        $this->assertContains('OrderPlaced', $output);
    }

    /** @test */
    public function it_can_display_congratulations_message(): void
    {
        $this->givenMessagesWereCollected(new OrderConfirmed(Uuid::uuid4(), 5));

        $output = $this->display();

        $this->assertContains('Congratulations!', $output);
    }

    /** @test */
    public function it_can_display_payload_of_message(): void
    {
        $this->givenMessagesWereCollected($message = new OrderPlaced(Uuid::uuid4(), 5));

        $output = $this->display();

        $this->assertContains(json_encode($message->payload()), $output);
    }

    /** @test */
    public function it_can_display_table_with_multiple_messages(): void
    {
        $this->givenMessagesWereCollected(
            new PlaceOrder(Uuid::uuid4(), 5),
            new OrderPlaced(Uuid::uuid4(), 5)
        );

        $output = $this->display();

        $this->assertContains('Command', $output);
        $this->assertContains('DomainEvent', $output);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->collectsMessages  = $this->container()->get(MessageCollector::class);
        $this->tableWithMessages = $this->container()->get(TableWithMessages::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->collectsMessages  = null;
        $this->tableWithMessages = null;
    }

    private function givenMessagesWereCollected(...$messages): void
    {
        foreach ($messages as $message) {
            $actionEvent = $this->prophesize(ActionEvent::class);

            $actionEvent->getParam(Argument::any())
                ->willReturn($message);

            ($this->collectsMessages)($actionEvent->reveal());
        }
    }

    private function display(): string
    {
        $this->tableWithMessages->display($output = new BufferedOutput());

        return $output->fetch();
    }
}
