<?php

declare(strict_types=1);

namespace Infrastructure\Console;

use Infrastructure\Console\Output\TableWithMessages;
use Messaging\Command\CreateOrder;
use Messaging\Command\MakeReservation;
use Prooph\ServiceBus\CommandBus;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class PlaygroundCommand extends Command
{
    const CHOICES = [
        1 => 'Create Order',
        2 => 'Make Reservation',
        3 => 'Make Payment',
        4 => 'Add seats to WaitList',
    ];

    /** @var CommandBus */
    private $commandBus;

    /** @var TableWithMessages */
    private $tableWithMessages;

    public function __construct(CommandBus $commandBus, TableWithMessages $tableWithMessages)
    {
        parent::__construct('prooph:saga:playground');

        $this->commandBus        = $commandBus;
        $this->tableWithMessages = $tableWithMessages;
    }

    protected function configure()
    {
        $this
            ->setDescription('Order processing with a saga and process managers.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('What would you like to do?');

        if (
            false === $answer = $this->getHelper('question')
                ->ask($input, $output, new ChoiceQuestion('', self::CHOICES))
        ) {
            return 0;
        }

        if (self::CHOICES[1] === $answer) {
            //todo: Ask number of seats
            $this->commandBus->dispatch(new CreateOrder(Uuid::uuid4(), 5));
        }

        if (self::CHOICES[2] === $answer) {
            $this->commandBus->dispatch(new MakeReservation(Uuid::uuid4(), 5));
        }

        $this->tableWithMessages->display($output);

        return 0;
    }
}
