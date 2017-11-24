<?php

declare(strict_types=1);

namespace tests;

use Infrastructure\Middleware\CollectsMessages;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\EventBus;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;

abstract class UsesScenarioTestCase extends UsesContainerTestCase
{
    /** @var Scenario */
    private $scenario;

    /** @var TestAggregateIdFactory */
    private $aggregateIdFactory;

    protected function setUp()
    {
        parent::setUp();

        $this->scenario = new Scenario(
            $this->container()->get(CommandBus::class),
            $this->container()->get(EventBus::class),
            $this->container()->get(CollectsMessages::class),
            $this
        );

        $this->aggregateIdFactory = new TestAggregateIdFactory();
        Uuid::setFactory($this->aggregateIdFactory);
    }

    protected function scenario(): Scenario
    {
        return $this->scenario;
    }

    protected function tearDown()
    {
        Uuid::setFactory(new UuidFactory());

        $this->scenario           = null;
        $this->aggregateIdFactory = null;

        parent::tearDown();
    }

    protected function lastGeneratedAggregateIds(): array
    {
        $allIds = $this->aggregateIdFactory->all();

        if (empty($allIds)) {
            $this->fail('No aggregateIds generated.');
        }

        return $allIds;
    }
}
