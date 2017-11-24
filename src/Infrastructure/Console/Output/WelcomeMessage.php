<?php

declare(strict_types=1);

namespace Infrastructure\Console\Output;

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

    public function display(OutputInterface $output)
    {
        $output->writeln([
            sprintf(
                'There is "%d" seats available, price per seat is "%d"',
                $this->numberOfSeatsAvailable,
                $this->pricePerSeat
            ),
            'What would you like to do?',
        ]);
    }
}
