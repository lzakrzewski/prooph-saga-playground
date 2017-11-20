<?php

declare(strict_types=1);

namespace Application\Command;

class MakeReservationHandler
{
    /** @var ReservationRepository */
    private $reservationRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->reservationRepository = $orderRepository;
    }

    public function __invoke(CreateOrder $command)
    {
        $this->reservationRepository->save(Order::create($command->orderId, $command->numberOfSeats));
    }
}
