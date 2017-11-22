<?php

declare(strict_types=1);

namespace Console\Container;

use Console\Middleware\CollectsMessages;
use Console\PlaygroundCommand;
use Messaging\Command\AddSeatsToWaitList;
use Messaging\Command\CreateOrder;
use Messaging\Command\Handler\AddSeatsToWaitListHandler;
use Messaging\Command\Handler\CreateOrderHandler;
use Messaging\Command\Handler\MakePaymentHandler;
use Messaging\Command\Handler\MakeReservationHandler;
use Messaging\Command\MakePayment;
use Messaging\Command\MakeReservation;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\Router\CommandRouter;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

final class Container implements ContainerInterface
{
    /** @var array */
    private $contents;

    public static function build(): self
    {
        return new self();
    }

    public function get($id)
    {
        if (false === $this->has($id)) {
            throw NotFoundException::with($id);
        }

        return $this->contents[$id];
    }

    public function has($id)
    {
        return isset($this->contents[$id]);
    }

    private function __construct()
    {
        $this->contents = $this
            ->console(
                $this
                    ->messaging(
                        $this
                            ->config()
                    )
            );
    }

    private function config(): array
    {
        return \Config::get();
    }

    private function messaging(array $contents): array
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
            ->to(new MakeReservationHandler($eventBus, $contents[\Config::AVAILABLE_SEATS]))
            ->route(MakePayment::class)
            ->to(new MakePaymentHandler($eventBus))
            ->route(AddSeatsToWaitList::class)
            ->to(new AddSeatsToWaitListHandler($eventBus));

        $commandRouter
            ->attachToMessageBus($commandBus);

        return array_merge(
            $contents,
            [
                CollectsMessages::class => $middleware,
                EventBus::class         => $eventBus,
                CommandBus::class       => $commandBus,
            ]
        );
    }

    private function console(array $contents): array
    {
        $application    = new Application();
        $consoleCommand = new PlaygroundCommand($contents[CommandBus::class], $contents[CollectsMessages::class]);

        $application->add($consoleCommand);

        return array_merge(
            $contents,
            [
                PlaygroundCommand::class => $consoleCommand,
                Application::class       => $application,
            ]
        );
    }
}
