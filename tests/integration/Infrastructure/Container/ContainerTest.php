<?php

declare(strict_types=1);

namespace tests\integration\Infrastructure\Container;

use Infrastructure\Container\Container;
use Infrastructure\Container\NotFoundException;
use Infrastructure\Listener\CollectsMessages;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    /** @test */
    public function it_knows_when_service_exists(): void
    {
        $this->assertTrue(
            Container::build()->has(CollectsMessages::class)
        );
    }

    /** @test */
    public function it_knows_when_service_does_not_exists(): void
    {
        $this->assertFalse(
            Container::build()->has('unknown')
        );
    }

    /** @test */
    public function it_can_get_service(): void
    {
        $this->assertInstanceOf(
            CollectsMessages::class,
            Container::build()->get(CollectsMessages::class)
        );
    }

    /** @test */
    public function it_fails_when_service_does_not_exist(): void
    {
        $this->expectException(NotFoundException::class);

        $this->assertInstanceOf(
            CollectsMessages::class,
            Container::build()->get('unknown')
        );
    }
}
