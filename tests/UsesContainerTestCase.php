<?php

declare(strict_types=1);

namespace tests;

use Container\ContainerBuilder;
use PHPUnit\Framework\TestCase;

abstract class UsesContainerTestCase extends TestCase
{
    /** @var array */
    private $container;

    protected function setUp()
    {
        $this->container = ContainerBuilder::build();
    }

    protected function tearDown()
    {
        $this->container = null;
    }

    protected function container(): array
    {
        return $this->container;
    }
}
