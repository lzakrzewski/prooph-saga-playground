<?php

declare(strict_types=1);

namespace Infrastructure\Console\Display;

use Messaging\Command;
use Messaging\Command\AddSeatsToWaitList;
use Messaging\Command\CreateOrder;
use Messaging\Command\MakePayment;
use Messaging\Command\MakeReservation;
use Prooph\ServiceBus\CommandBus;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class Questions
{
    const CHOICES = [
        1 => 'Create Order',
        2 => 'Make Reservation',
        3 => 'Make Payment',
        4 => 'Add seats to WaitList',
    ];

    /** @var CommandBus */
    private $commandBus;

    /** @var QuestionHelper */
    private $questionHelper;

    public function __construct(CommandBus $commandBus, QuestionHelper $questionHelper)
    {
        $this->commandBus     = $commandBus;
        $this->questionHelper = $questionHelper;
    }

    public function display(InputInterface $input, OutputInterface $output): void
    {
        $answer = $this
            ->questionHelper
            ->ask($input, $output, new ChoiceQuestion('', self::CHOICES));

        if (false === $answer) {
            return;
        }

        if (self::CHOICES[1] === $answer) {
            $this->dispatch(
                new CreateOrder(Uuid::uuid4(), $this->askForNumberOfSeats($input, $output))
            );
        }

        if (self::CHOICES[2] === $answer) {
            $this->dispatch(
                new MakeReservation(Uuid::uuid4(), Uuid::uuid4(), $this->askForNumberOfSeats($input, $output))
            );
        }

        if (self::CHOICES[3] === $answer) {
            $this->dispatch(
                new MakePayment(Uuid::uuid4(), Uuid::uuid4(), $this->askForAmount($input, $output))
            );
        }

        if (self::CHOICES[4] === $answer) {
            $this->dispatch(
                new AddSeatsToWaitList(Uuid::uuid4(), $this->askForNumberOfSeats($input, $output))
            );
        }
    }

    private function dispatch(Command $command): void
    {
        $this->commandBus->dispatch($command);
    }

    private function askForNumberOfSeats(InputInterface $input, OutputInterface $output): int
    {
        return (int) $this
            ->questionHelper
            ->ask($input, $output, new Question('How many seats? ', 5));
    }

    private function askForAmount(InputInterface $input, OutputInterface $output): int
    {
        return (int) $this
            ->questionHelper
            ->ask($input, $output, new Question('How much? ', 100));
    }
}
