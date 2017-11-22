<?php

declare(strict_types=1);

namespace tests;

use Console\Container;
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

    //Todo: rename it to get / use psr container
    protected function getFromContainer(string $content)
    {
        return ($this->container)($content);
    }
}
