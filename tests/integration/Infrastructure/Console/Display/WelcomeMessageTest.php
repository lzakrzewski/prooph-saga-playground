<?php

declare(strict_types=1);

namespace tests\integration\Infrastructure\Console\Display;

use Infrastructure\Console\Display\WelcomeMessage;
use Symfony\Component\Console\Output\BufferedOutput;
use tests\ContainerTestCase;

class WelcomeMessageTest extends ContainerTestCase
{
    /** @var WelcomeMessage */
    private $welcomeMessage;

    /** @test */
    public function it_can_display_welcome_message(): void
    {
        $output = $this->display();

        $this->assertContains((string) $this->container()->get(\Config::PRICE_PER_SEAT), $output);
        $this->assertContains((string) $this->container()->get(\Config::AVAILABLE_SEATS), $output);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->welcomeMessage = $this->container()->get(WelcomeMessage::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->welcomeMessage = null;
    }

    private function display(): string
    {
        $this->welcomeMessage->display($output = new BufferedOutput());

        return $output->fetch();
    }
}
