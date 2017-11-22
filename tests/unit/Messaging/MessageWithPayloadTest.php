<?php

declare(strict_types=1);

namespace tests\unit\Messaging;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use tests\unit\Messaging\fixtures\TestMessage;
use tests\unit\Messaging\fixtures\TestMessageWithPrivateField;

class MessageWithPayloadTest extends TestCase
{
    /** @test */
    public function message_can_have_payload()
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

    /** @test */
    public function payload_does_not_contain_values_of_private_fields()
    {
        $message = new TestMessageWithPrivateField($messageId = Uuid::uuid4(), 954);

        $this->assertEquals(
            [
                'messageId' => $messageId->toString(),
            ],
            $message->payload()
        );
    }
}
