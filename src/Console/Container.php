<?php

declare(strict_types=1);

namespace Console;

use Console\Middleware\CollectsMessages;
use Playground\Command\AddSeatsToWaitList;
use Playground\Command\AddSeatsToWaitListHandler;
use Playground\Command\CreateOrder;
use Playground\Command\CreateOrderHandler;
use Playground\Command\MakePayment;
use Playground\Command\MakePaymentHandler;
use Playground\Command\MakeReservation;
use Playground\Command\MakeReservationHandler;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\Router\CommandRouter;
use Symfony\Component\Console\Application;

final class Container
{
    /** @var array */
    private $contents = [];

    //Todo: get rid of this mess
    public function __construct()
    {
        $this->config();
        $this->messaging();
        $this->console();
    }

    public function __invoke(string $service)
    {
        if (false === isset($this->contents[$service])) {
            throw new \InvalidArgumentException(sprintf('Service "%s" does not exist.', $service));
        }

        return $this->contents[$service];
    }

    private function config()
    {
        $this->contents = array_merge(
            $this->contents,
            [
                \Config::AVAILABLE_SEATS => \Config::parameters()[\Config::AVAILABLE_SEATS],
            ]
        );
    }

    private function messaging()
    {
        $commandRouter = new CommandRouter();
        $middleware    = new CollectsMessages();
        $commandBus    = new CommandBus();
        $eventBus      = new EventBus();

        $eventBus
            ->attach(MessageBus::EVENT_DISPATCH, $middleware);
        $commandBus
            ->attach(MessageBus::EVENT_DISPATCH, $middleware);

        $commandRouter
            ->route(CreateOrder::class)
            ->to(new CreateOrderHandler($eventBus))
            ->route(MakeReservation::class)
            ->to(new MakeReservationHandler($eventBus, $this->contents[\Config::AVAILABLE_SEATS]))
            ->route(MakePayment::class)
            ->to(new MakePaymentHandler($eventBus))
            ->route(AddSeatsToWaitList::class)
            ->to(new AddSeatsToWaitListHandler($eventBus));

        $commandRouter
            ->attachToMessageBus($commandBus);

        $this->contents = array_merge(
            $this->contents,
            [
                CollectsMessages::class => $middleware,
                EventBus::class         => $eventBus,
                CommandBus::class       => $commandBus,
            ]
        );
    }

    private function console()
    {
        $application    = new Application();
        $consoleCommand = new PlaygroundCommand($this->contents[CommandBus::class], $this->contents[CollectsMessages::class]);

        $application->add($consoleCommand);

        $this->contents = array_merge(
            $this->contents,
            [
                PlaygroundCommand::class => $consoleCommand,
                Application::class       => $application,
            ]
        );
    }
}
