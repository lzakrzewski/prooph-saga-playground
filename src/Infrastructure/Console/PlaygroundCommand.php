<?php

declare(strict_types=1);

namespace Infrastructure\Console;

use Infrastructure\Console\Display\Questions;
use Infrastructure\Console\Display\TableWithMessages;
use Infrastructure\Console\Display\WelcomeMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PlaygroundCommand extends Command
{
    /** @var WelcomeMessage */
    private $welcomeMessage;

    /** @var Questions */
    private $questions;

    /** @var TableWithMessages */
    private $tableWithMessages;

    public function __construct(
        WelcomeMessage $welcomeMessage,
        Questions $questions,
        TableWithMessages $tableWithMessages
    ) {
        parent::__construct('prooph:saga:playground');

        $this->welcomeMessage    = $welcomeMessage;
        $this->questions         = $questions;
        $this->tableWithMessages = $tableWithMessages;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Order processing with saga pattern.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->welcomeMessage->display($output);
        $this->questions->display($input, $output);
        $this->tableWithMessages->display($output);

        return 0;
    }
}
