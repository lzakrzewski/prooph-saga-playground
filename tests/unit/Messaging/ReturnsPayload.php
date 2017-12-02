<?php

declare(strict_types=1);

namespace tests\unit\Messaging;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use tests\unit\Messaging\fixtures\TestMessage;

class ReturnsPayload extends TestCase
{
    /** @test */
    public function it_can_return_payload(): void
    {
        $message = new TestMessage($messageId = Uuid::uuid4(), 954);

        $this->assertEquals(
            [
                'messageId' => $messageId->toString(),
                'value'     => '954',
            ],
            $message->payload()
        );
    }
}
