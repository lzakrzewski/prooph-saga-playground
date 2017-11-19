<?php

declare(strict_types=1);

namespace Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PlaygroundCommand extends Command
{
    public function __construct()
    {
        parent::__construct('prooph:saga:playground');
    }

    protected function configure()
    {
        $this
            ->setDescription('Order processing with a saga and process managers.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return 0;
    }
}
