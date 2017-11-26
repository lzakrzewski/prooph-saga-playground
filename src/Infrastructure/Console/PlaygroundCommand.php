<?php

declare(strict_types=1);

namespace Infrastructure\Console;

use Infrastructure\Console\Output\TableWithMessages;
use Infrastructure\Console\Output\WelcomeMessage;
use Messaging\Command\AddSeatsToWaitList;
use Messaging\Command\CreateOrder;
use Messaging\Command\MakePayment;
use Messaging\Command\MakeReservation;
use Prooph\ServiceBus\CommandBus;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

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

    /** @var WelcomeMessage */
    private $welcomeMessage;

    public function __construct(
        CommandBus $commandBus,
        TableWithMessages $tableWithMessages,
        WelcomeMessage $welcomeMessage
    ) {
        parent::__construct('prooph:saga:playground');

        $this->commandBus        = $commandBus;
        $this->tableWithMessages = $tableWithMessages;
        $this->welcomeMessage    = $welcomeMessage;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Order processing with a saga and process managers.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->welcomeMessage->display($output);

        //Todo: encapsulate ask questions logic
        if (
            false === $answer = $this->getHelper('question')
                ->ask($input, $output, new ChoiceQuestion('', self::CHOICES))
        ) {
            return 0;
        }

        if (self::CHOICES[1] === $answer) {
            $numberOfSeats = $this->getHelper('question')
                ->ask($input, $output, new Question('How many seats? ', 5));

            if (false === $numberOfSeats) {
                return 0;
            }

            $this->commandBus->dispatch(new CreateOrder(Uuid::uuid4(), (int) $numberOfSeats));
        }

        if (self::CHOICES[2] === $answer) {
            $numberOfSeats = $this->getHelper('question')
                ->ask($input, $output, new Question('How many seats? ', 5));

            if (false === $numberOfSeats) {
                return 0;
            }

            $this->commandBus->dispatch(new MakeReservation(Uuid::uuid4(), (int) $numberOfSeats));
        }

        if (self::CHOICES[3] === $answer) {
            $amount = $this->getHelper('question')
                ->ask($input, $output, new Question('How  much? ', 500));

            if (false === $amount) {
                return 0;
            }

            $this->commandBus->dispatch(new MakePayment(Uuid::uuid4(), (int) $amount));
        }

        if (self::CHOICES[4] === $answer) {
            $numberOfSeats = $this->getHelper('question')
                ->ask($input, $output, new Question('How many seats? ', 5));

            if (false === $numberOfSeats) {
                return 0;
            }

            $this->commandBus->dispatch(new AddSeatsToWaitList(Uuid::uuid4(), 5));
        }

        $this->tableWithMessages->display($output);

        return 0;
    }
}
