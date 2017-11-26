<?php

declare(strict_types=1);

namespace tests\unit\Messaging;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use tests\unit\Messaging\fixtures\TestMessage;

class MessageWithPayloadTest extends TestCase
{
    /** @test */
    public function message_can_have_payload(): void
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
