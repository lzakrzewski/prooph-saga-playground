<?php

declare(strict_types=1);

namespace Infrastructure\Console;

use Application\Command\CreateOrder;
use Application\Command\MakeReservation;
use Application\Middleware\CollectsMessages;
use Prooph\Common\Messaging\DomainEvent;
use Prooph\ServiceBus\CommandBus;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
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

    /** @var CollectsMessages */
    private $collectsMessages;

    public function __construct(CommandBus $commandBus, CollectsMessages $collectsMessages)
    {
        parent::__construct('prooph:saga:playground');

        $this->commandBus       = $commandBus;
        $this->collectsMessages = $collectsMessages;
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

                $this->display($output);

                return 0;
            }

            if (self::CHOICES[2] === $answer) {
                $this->commandBus->dispatch(new MakeReservation(Uuid::uuid4(), 5));

                $this->display($output);

                return 0;
            }

            if (false === $answer) {
                return 0;
            }
        }

        return 0;
    }

    private function display(OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(['Name of message', 'type']);

        $messages = array_reverse($this->collectsMessages->all());

        foreach ($messages as $message) {
            $shortName = (new \ReflectionClass($message))->getShortName();

            if ($message instanceof DomainEvent) {
                $table->addRow(['<comment>'.$shortName.'</comment>', '<comment>Event</comment>']);
                continue;
            }

            $table->addRow(['<info>'.$shortName.'</info>', '<info>Command</info>']);
        }

        $table->render();
    }
}
