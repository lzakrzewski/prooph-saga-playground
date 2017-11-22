<?php

declare(strict_types=1);

namespace tests;

use Console\Middleware\CollectsMessages;
use Prooph\ServiceBus\CommandBus;

abstract class UsesScenarioTestCase extends UsesContainerTestCase
{
    /** @var Scenario */
    private $scenario;

    protected function setUp()
    {
        parent::setUp();

        $this->scenario = new Scenario(
            $this->container()->get(CommandBus::class),
            $this->container()->get(CollectsMessages::class),
            $this
        );
    }

    protected function scenario(): Scenario
    {
        return $this->scenario;
    }

    protected function tearDown()
    {
        $this->scenario = null;

        parent::tearDown();
    }
}
