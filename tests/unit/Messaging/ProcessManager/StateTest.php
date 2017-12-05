<?php

declare(strict_types=1);

namespace tests\unit\Messaging\ProcessManager;

use Messaging\ProcessManager\State;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class StateTest extends TestCase
{
    /** @test */
    public function it_can_be_started(): void
    {
        $state = State::start($processId = Uuid::uuid4(), ['key' => 'value']);

        $this->assertInstanceOf(State::class, $state);
        $this->assertTrue($processId->equals($state->processId()));
        $this->assertEquals(['key' => 'value'], $state->payload());
    }

    /** @test */
    public function it_can_apply_to_payload(): void
    {
        $state = State::start($processId = Uuid::uuid4(), ['key' => 'value'])
            ->apply(['another_key' => 'another_value']);

        $this->assertInstanceOf(State::class, $state);
        $this->assertTrue($processId->equals($state->processId()));
        $this->assertEquals(
            [
                'key'         => 'value',
                'another_key' => 'another_value',
            ],
            $state->payload()
        );
    }

    /** @test */
    public function it_overrides_payload(): void
    {
        $state = State::start($processId = Uuid::uuid4(), ['key' => 'value'])
            ->apply(['key' => 'another_value']);

        $this->assertInstanceOf(State::class, $state);
        $this->assertTrue($processId->equals($state->processId()));
        $this->assertEquals(['key' => 'another_value'], $state->payload());
    }

    /** @test */
    public function it_knows_when_it_has_value_in_payload(): void
    {
        $state = State::start(Uuid::uuid4(), ['key' => 'value']);

        $this->assertTrue($state->has('key'));
    }

    /** @test */
    public function it_knows_when_it_does_not_have_value_in_payload(): void
    {
        $state = State::start(Uuid::uuid4(), ['key' => 'value']);

        $this->assertFalse($state->has('another_key'));
    }

    /** @test */
    public function it_knows_when_it_has_empty_value_in_payload(): void
    {
        $state = State::start(Uuid::uuid4(), ['key' => '']);

        $this->assertFalse($state->has('key'));
    }

    /** @test */
    public function it_can_not_be_modified_when_its_done(): void
    {
        $this->expectException(\DomainException::class);

        State::start($processId = Uuid::uuid4(), ['key' => 'value'])
            ->done()
            ->apply(['some_key' => 'some_value']);
    }

    /** @test */
    public function its_immutable(): void
    {
        $state1 = State::start(Uuid::uuid4(), ['key' => 'value']);
        $state2 = $state1->apply(['key' => 'value']);

        $this->assertNotSame($state1, $state2);
    }
}
