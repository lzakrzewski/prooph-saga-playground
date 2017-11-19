<?php

declare(strict_types=1);

namespace tests\integration;

use Console\PlaygroundCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use tests\UsesContainerTestCase;

class PlaygroundCommandTest extends UsesContainerTestCase
{
    /** @var CommandTester */
    private $tester;

    /** @test */
    public function it_returns_0_exit_code_after_success()
    {
        $command = $this->container()[PlaygroundCommand::class];

        $this->executeConsoleCommand($command);

        $this->assertExitCode(0);
    }

    private function executeConsoleCommand(Command $cli, array $inputs = [])
    {
        $this->tester = new CommandTester($cli);
        $this->tester->setInputs($inputs);
        $this->tester->execute([]);
    }

    private function assertExitCode(int $expectedStatus)
    {
        $this->assertEquals($expectedStatus, $this->tester->getStatusCode());
    }
}
