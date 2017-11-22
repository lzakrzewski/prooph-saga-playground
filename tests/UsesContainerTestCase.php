<?php

declare(strict_types=1);

namespace tests;

use Console\Container\Container;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

abstract class UsesContainerTestCase extends TestCase
{
    /** @var Container */
    private $container;

    protected function setUp()
    {
        $this->container = Container::build();
    }

    protected function container(): ContainerInterface
    {
        return $this->container;
    }

    protected function tearDown()
    {
        $this->container = null;
    }
}
