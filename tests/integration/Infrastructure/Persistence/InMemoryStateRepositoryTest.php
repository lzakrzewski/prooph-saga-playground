<?php

declare(strict_types=1);

namespace tests\integration\Infrastructure\Persistence;

use Infrastructure\Persistence\InMemoryStateRepository;
use Messaging\ProcessManager\State;
use Messaging\ProcessManager\StateRepository;
use Ramsey\Uuid\Uuid;
use tests\ContainerTestCase;

class InMemoryStateRepositoryTest extends ContainerTestCase
{
    /** @var InMemoryStateRepository */
    private $stateRepository;

    /** @test */
    public function it_can_find_state_by_process_id(): void
    {
        $this->stateRepository->save(
            State::start($processId = Uuid::uuid4(), $payload = ['someId' => Uuid::uuid4()])
        );

        $state = $this->stateRepository->find($processId);

        $this->assertInstanceOf(State::class, $state);
        $this->assertTrue($processId->equals($state->processId()));
        $this->assertEquals($payload, $state->payload());
    }

    /** @test */
    public function it_can_update_state(): void
    {
        $this->given(State::start($processId = Uuid::uuid4(), ['someId' => Uuid::uuid4()]));

        $this->stateRepository->save(
            State::start($processId, $payload = ['someId' => Uuid::uuid4(), 'a' => 'b'])
        );

        $state = $this->stateRepository->find($processId);

        $this->assertInstanceOf(State::class, $state);
        $this->assertTrue($processId->equals($state->processId()));
        $this->assertEquals($payload, $state->payload());
    }

    /** @test */
    public function it_returns_null_when_state_for_given_criteria_does_not_exist(): void
    {
        $this->given(State::start(Uuid::uuid4(), ['someId' => Uuid::uuid4()]));

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
