<?php

declare(strict_types=1);

namespace Container;

use Console\PlaygroundCommand;
use Symfony\Component\Console\Application;

final class ContainerBuilder
{
    public static function build(): array
    {
        $command = new PlaygroundCommand();

        $application = new Application();
        $application->add($command);

        return [
            PlaygroundCommand::class => $command,
            Application::class       => $application,
        ];
    }
}
