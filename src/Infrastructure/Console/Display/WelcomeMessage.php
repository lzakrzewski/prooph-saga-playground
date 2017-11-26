<?php

declare(strict_types=1);

namespace Infrastructure\Console\Display;

use Symfony\Component\Console\Output\OutputInterface;

class WelcomeMessage
{
    /** @var int */
    private $numberOfSeatsAvailable;

    /** @var int */
    private $pricePerSeat;

    public function __construct(int $numberOfSeatsAvailable, int $priceForSeat)
    {
        $this->numberOfSeatsAvailable = $numberOfSeatsAvailable;
        $this->pricePerSeat           = $priceForSeat;
    }

    public function display(OutputInterface $output): void
    {
        $output->writeln([
            sprintf(
                'There is "<info>%d</info>" seats available, price per seat is "<info>%d</info>".',
                $this->numberOfSeatsAvailable,
                $this->pricePerSeat
            ),
            '',
            'What would you like to do?',
        ]);
    }
}
