<?php

declare(strict_types=1);

namespace tests\integration\Infrastructure\Persistence;

use Infrastructure\Persistence\InMemoryStateRepository;
use Messaging\Saga\State;
use Messaging\Saga\StateRepository;
use Ramsey\Uuid\Uuid;
use tests\UsesContainerTestCase;

class InMemoryStateRepositoryTest extends UsesContainerTestCase
{
    /** @var InMemoryStateRepository */
    private $stateRepository;

    /** @test */
    public function it_can_find_state_by_saga_id(): void
    {
        $this->stateRepository->save(
            State::create($sagaId = Uuid::uuid4(), $payload = ['someId' => Uuid::uuid4()])
        );

        $state = $this->stateRepository->find($sagaId);

        $this->assertInstanceOf(State::class, $state);
        $this->assertTrue($sagaId->equals($state->sagaId()));
        $this->assertEquals($payload, $state->payload());
    }

    /** @test */
    public function it_can_update_state(): void
    {
        $this->given(State::create($sagaId = Uuid::uuid4(), ['someId' => Uuid::uuid4()]));

        $this->stateRepository->save(
            State::create($sagaId, $payload = ['someId' => Uuid::uuid4(), 'a' => 'b'])
        );

        $state = $this->stateRepository->find($sagaId);

        $this->assertInstanceOf(State::class, $state);
        $this->assertTrue($sagaId->equals($state->sagaId()));
        $this->assertEquals($payload, $state->payload());
    }

    /** @test */
    public function it_returns_null_when_state_for_given_criteria_does_not_exist(): void
    {
        $this->given(State::create(Uuid::uuid4(), ['someId' => Uuid::uuid4()]));

        $this->assertNull($this->stateRepository->find(Uuid::uuid4()));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->stateRepository = $this->container()->get(StateRepository::class);
    }

    protected function tearDown(): void
    {
        $this->stateRepository = null;

        parent::tearDown();
    }

    private function given(State $state): void
    {
        $this->stateRepository->save($state);
    }
}
