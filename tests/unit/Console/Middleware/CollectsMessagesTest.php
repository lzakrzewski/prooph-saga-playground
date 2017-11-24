<?php

declare(strict_types=1);

namespace tests\unit\Console\Middleware;

use Console\Middleware\CollectsMessages;
use Messaging\Command\CreateOrder;
use Messaging\Event\OrderCreated;
use PHPUnit\Framework\TestCase;
use Prooph\Common\Event\ActionEvent;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Ramsey\Uuid\Uuid;

class CollectsMessagesTest extends TestCase
{
    /** @var CollectsMessages */
    private $collectsMessages;

    /** @var ActionEvent|ObjectProphecy */
    private $actionEvent;

    /** @test */
    public function it_collects_various_messages()
    {
        $this->collectMessages($messages = [
            new CreateOrder($orderId = Uuid::uuid4(), 5),
            new OrderCreated($orderId, 5),
        ]);

        $this->assertEquals($messages, $this->collectsMessages->all());
    }

    /** @test */
    public function it_does_not_record_unknown_messages()
    {
        $this->collectMessages([
            'unknown',
        ]);

        $this->assertEmpty($this->collectsMessages->all());
    }

    /** @test */
    public function it_can_not_get_all_recorded_messages_twice()
    {
        $this->collectMessages([
            new CreateOrder($orderId = Uuid::uuid4(), 5),
        ]);

        $this->collectsMessages->all();

        $this->assertEmpty($this->collectsMessages->all());
    }

    public function setUp()
    {
        $this->actionEvent = $this->prophesize(ActionEvent::class);

        $this->collectsMessages = new CollectsMessages();
    }

    protected function tearDown()
    {
        $this->collectsMessages = null;
    }

    private function collectMessages(array $messages)
    {
        $collectsMessages = $this->collectsMessages;

        foreach ($messages as $message) {
            $this->actionEvent->getParam(Argument::any())->willReturn($message);

            $collectsMessages($this->actionEvent->reveal());
        }
    }
}
