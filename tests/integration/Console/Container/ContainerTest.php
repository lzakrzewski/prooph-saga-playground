<?php

declare(strict_types=1);

namespace tests\integration\Console;

use Console\Container\Container;
use Console\Container\NotFoundException;
use Console\Middleware\CollectsMessages;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    /** @test */
    public function it_knows_when_service_exists()
    {
        $this->assertTrue(
            Container::build()->has(CollectsMessages::class)
        );
    }

    /** @test */
    public function it_knows_when_service_does_not_exists()
    {
        $this->assertFalse(
            Container::build()->has('unknown')
        );
    }

    /** @test */
    public function it_can_get_service()
    {
        $this->assertInstanceOf(
            CollectsMessages::class,
            Container::build()->get(CollectsMessages::class)
        );
    }

    /** @test */
    public function it_fails_when_service_does_not_exist()
    {
        $this->expectException(NotFoundException::class);

        $this->assertInstanceOf(
            CollectsMessages::class,
            Container::build()->get('unknown')
        );
    }
}
