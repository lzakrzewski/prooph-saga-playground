<?php

declare(strict_types=1);

namespace tests;

use Infrastructure\Container\Container;
use Messaging\Saga\StateRepository;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

abstract class UsesContainerTestCase extends TestCase
{
    /** @var Container */
    private $container;

    protected function setUp()
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

    protected function tearDown()
    {
        $this->container = null;
    }
}
