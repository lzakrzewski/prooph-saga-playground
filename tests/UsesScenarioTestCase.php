<?php

declare(strict_types=1);

namespace tests;

use Prooph\EventStore\EventStore;
use Prooph\ServiceBus\CommandBus;

abstract class UsesScenarioTestCase extends UsesContainerTestCase
{
    /** @var Scenario */
    protected $scenario;

    protected function setUp()
    {
        parent::setUp();

        $this->scenario = new Scenario(
            $this->getService(CommandBus::class),
            $this->getService(EventStore::class),
            $this
        );
    }

    protected function tearDown()
    {
        $this->scenario = null;

        parent::tearDown();
    }
}
