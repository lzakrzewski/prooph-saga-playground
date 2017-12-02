<?php

declare(strict_types=1);

namespace tests\unit\Infrastructure\Listener;

use Infrastructure\Listener\MessageCollector;
use Messaging\Command\PlaceOrder;
use Messaging\Event\OrderPlaced;
use PHPUnit\Framework\TestCase;
use Prooph\Common\Event\ActionEvent;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Ramsey\Uuid\Uuid;

class MessageCollectorTest extends TestCase
{
    /** @var MessageCollector */
    private $collectsMessages;

    /** @var ActionEvent|ObjectProphecy */
    private $actionEvent;

    /** @test */
    public function it_collects_various_messages(): void
    {
        $this->collectMessages($messages = [
            new PlaceOrder($orderId = Uuid::uuid4(), 5),
            new OrderPlaced($orderId, 5),
        ]);

        $this->assertEquals($messages, $this->collectsMessages->all());
    }

    /** @test */
    public function it_does_not_collect_unknown_messages(): void
    {
        $this->collectMessages([
            'unknown',
        ]);

        $this->assertEmpty($this->collectsMessages->all());
    }

    public function setUp(): void
    {
        $this->actionEvent = $this->prophesize(ActionEvent::class);

        $this->collectsMessages = new MessageCollector();
    }

    protected function tearDown(): void
    {
        $this->collectsMessages = null;
    }

    private function collectMessages(array $messages): void
    {
        $collectsMessages = $this->collectsMessages;

        foreach ($messages as $message) {
            $this->actionEvent->getParam(Argument::any())->willReturn($message);

            $collectsMessages($this->actionEvent->reveal());
        }
    }
}
