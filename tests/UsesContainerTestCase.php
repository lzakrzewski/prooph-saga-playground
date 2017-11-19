<?php

declare(strict_types=1);

namespace tests;

use Infrastructure\Container\Container;
use PHPUnit\Framework\TestCase;

abstract class UsesContainerTestCase extends TestCase
{
    /** @var Container */
    private $container;

    protected function setUp()
    {
        $this->container = new Container();
    }

    protected function tearDown()
    {
        $this->container = null;
    }

    protected function getService(string $service)
    {
        return ($this->container)($service);
    }
}
