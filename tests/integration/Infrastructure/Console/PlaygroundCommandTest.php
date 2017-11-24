<?php

declare(strict_types=1);

namespace tests\integration\Infrastructure\Console;

use Infrastructure\Console\PlaygroundCommand;
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
        $command = $this->container()->get(PlaygroundCommand::class);

        $this->executeConsoleCommand($command, ['1']);

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
