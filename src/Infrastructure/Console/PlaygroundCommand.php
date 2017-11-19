<?php

declare(strict_types=1);

namespace Infrastructure\Console;

use Application\Command\CreateOrder;
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

    public function __construct(CommandBus $commandBus)
    {
        parent::__construct('prooph:saga:playground');

        $this->commandBus = $commandBus;
    }

    protected function configure()
    {
        $this
            ->setDescription('Order processing with a saga and process managers.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        while (true) {
            $output->writeln('What would you like to do?');
            $helper   = $this->getHelper('question');
            $question = new ChoiceQuestion('', self::CHOICES);

            $answer = $helper->ask($input, $output, $question);

            if (self::CHOICES[1] === $answer) {
                $this->commandBus->dispatch(new CreateOrder(Uuid::uuid4(), 5));

                return 0;
            }

            if (false === $answer) {
                return 0;
            }
        }

        return 0;
    }
}
