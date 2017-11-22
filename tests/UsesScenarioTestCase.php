<?php

declare(strict_types=1);

namespace tests;

use Console\Middleware\CollectsMessages;
use Prooph\ServiceBus\CommandBus;

abstract class UsesScenarioTestCase extends UsesContainerTestCase
{
    /** @var Scenario */
    protected $scenario;

    protected function setUp()
    {
        parent::setUp();

        $this->scenario = new Scenario(
            $this->getFromContainer(CommandBus::class),
            $this->getFromContainer(CollectsMessages::class),
            $this
        );
    }

    protected function tearDown()
    {
        $this->scenario = null;

        parent::tearDown();
    }
}
