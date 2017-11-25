<?php

declare(strict_types=1);

namespace tests\unit\Messaging\State;

use Messaging\Saga\State;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class StateTest extends TestCase
{
    /** @test */
    public function it_can_be_created()
    {
        $state = State::create($sagaId = Uuid::uuid4(), ['key' => 'value']);

        $this->assertInstanceOf(State::class, $state);
        $this->assertTrue($sagaId->equals($state->sagaId()));
        $this->assertEquals(['key' => 'value'], $state->payload());
    }

    /** @test */
    public function it_can_apply_to_payload()
    {
        $state = State::create($sagaId = Uuid::uuid4(), ['key' => 'value'])
            ->apply(['another_key' => 'another_value']);

        $this->assertInstanceOf(State::class, $state);
        $this->assertTrue($sagaId->equals($state->sagaId()));
        $this->assertEquals(
            [
                'key'         => 'value',
                'another_key' => 'another_value',
            ],
            $state->payload()
        );
    }

    /** @test */
    public function it_overrides_payload()
    {
        $state = State::create($sagaId = Uuid::uuid4(), ['key' => 'value'])
            ->apply(['key' => 'another_value']);

        $this->assertInstanceOf(State::class, $state);
        $this->assertTrue($sagaId->equals($state->sagaId()));
        $this->assertEquals(['key' => 'another_value'], $state->payload());
    }

    /** @test */
    public function its_immutable()
    {
        $state1 = State::create(Uuid::uuid4(), ['key' => 'value']);
        $state2 = $state1->apply(['key' => 'value']);

        $this->assertNotSame($state1, $state2);
    }
}
