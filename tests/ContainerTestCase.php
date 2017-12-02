<?php

declare(strict_types=1);

namespace tests;

use Infrastructure\Container\Container;
use Messaging\ProcessManager\StateRepository;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

abstract class ContainerTestCase extends TestCase
{
    /** @var Container */
    private $container;

    protected function setUp(): void
    {
        $this->container = Container::build();
        $this->container
            ->get(StateRepository::class)
            ->reset();
    }

    protected function container(): ContainerInterface
    {
        return $this->container;
    }

    protected function tearDown(): void
    {
        $this->container = null;
    }
}
