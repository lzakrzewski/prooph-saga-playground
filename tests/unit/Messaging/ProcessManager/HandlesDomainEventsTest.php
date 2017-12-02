<?php

declare(strict_types=1);

namespace tests\acceptance\Messaging\ProcessManager;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use tests\unit\Messaging\fixtures\TestDomainEvent1;
use tests\unit\Messaging\fixtures\TestDomainEvent2;
use tests\unit\Messaging\fixtures\TestMessage;
use tests\unit\Messaging\fixtures\TestProcessManager;

class HandlesDomainEventsTest extends TestCase
{
    /** @var TestProcessManager */
    private $processManager;

    /** @test */
    public function it_can_handle_multiple_domain_events(): void
    {
        $this
            ->processManager
            ->handleThat($domainEvent1 = new TestMessage(Uuid::uuid4(), 929));

        $this
            ->processManager
            ->handleThat($domainEvent2 = new TestDomainEvent1(Uuid::uuid4(), 954));

        $this->assertEquals(
            $this->processManager->handledDomainEvents(),
            [
                $domainEvent1,
                $domainEvent2,
            ]
        );
    }

    /** @test */
    public function its_invokable(): void
    {
        ($this->processManager)($domainEvent = new TestDomainEvent1(Uuid::uuid4(), 636));

        $this->assertEquals($this->processManager->handledDomainEvents(), [$domainEvent]);
    }

    /** @test */
    public function it_can_not_handle_unknown_messages(): void
    {
        $this->expectException(\TypeError::class);

        ($this->processManager)(new \stdClass());
    }

    /** @test */
    public function it_can_not_handle_domain_events_when_specific_method_for_that_domain_event_does_not_exist(): void
    {
        $this->expectException(\BadMethodCallException::class);

        ($this->processManager)(new TestDomainEvent2(Uuid::uuid4(), 919));
    }

    protected function setUp(): void
    {
        $this->processManager = new TestProcessManager();
    }

    protected function tearDown(): void
    {
        $this->processManager = null;
    }
}
