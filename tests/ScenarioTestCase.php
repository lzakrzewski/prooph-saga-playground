<?php

declare(strict_types=1);

namespace tests;

use Infrastructure\Listener\MessageCollector;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\Plugin\Router\CommandRouter;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;

abstract class ScenarioTestCase extends ContainerTestCase
{
    /** @var Scenario */
    private $scenario;

    /** @var UuidCollector */
    private $collectsAggregateIds;

    protected function setUp(): void
    {
        parent::setUp();

        $this->scenario = new Scenario(
            $this->container()->get(CommandBus::class),
            $this->container()->get(CommandRouter::class),
            $this->container()->get(EventBus::class),
            $this->container()->get(MessageCollector::class),
            $this
        );

        $this->collectsAggregateIds = new UuidCollector();
        Uuid::setFactory($this->collectsAggregateIds);
    }

    protected function scenario(): Scenario
    {
        return $this->scenario;
    }

    protected function tearDown(): void
    {
        Uuid::setFactory(new UuidFactory());

        $this->scenario             = null;
        $this->collectsAggregateIds = null;

        parent::tearDown();
    }

    protected function uuids(): array
    {
        $allIds = $this->collectsAggregateIds->all();

        if (empty($allIds)) {
            $this->fail('No aggregateIds generated.');
        }

        return $allIds;
    }
}
