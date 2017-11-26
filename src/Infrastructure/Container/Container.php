<?php

declare(strict_types=1);

namespace Infrastructure\Container;

use Infrastructure\Console\Output\TableWithMessages;
use Infrastructure\Console\Output\WelcomeMessage;
use Infrastructure\Console\PlaygroundCommand;
use Infrastructure\Listener\CollectsMessages;
use Infrastructure\Persistence\InMemoryStateRepository;
use Messaging\Command\AddSeatsToWaitList;
use Messaging\Command\CreateOrder;
use Messaging\Command\Handler\AddSeatsToWaitListHandler;
use Messaging\Command\Handler\CreateOrderHandler;
use Messaging\Command\Handler\MakePaymentHandler;
use Messaging\Command\Handler\MakeReservationHandler;
use Messaging\Command\MakePayment;
use Messaging\Command\MakeReservation;
use Messaging\Event\OrderCreated;
use Messaging\Event\PaymentAccepted;
use Messaging\Event\SeatsNotReserved;
use Messaging\Event\SeatsReserved;
use Messaging\Saga\OrderSaga;
use Messaging\Saga\StateRepository;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\Router\CommandRouter;
use Prooph\ServiceBus\Plugin\Router\EventRouter;
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
        $commandRouter    = new CommandRouter();
        $eventRouter      = new EventRouter();
        $listener         = new CollectsMessages();
        $commandBus       = new CommandBus();
        $eventBus         = new EventBus();
        $stateRepository  = new InMemoryStateRepository();
        $saga             = new OrderSaga($commandBus, $eventBus, $stateRepository);

        $eventBus
            ->attach(MessageBus::EVENT_DISPATCH, $listener);
        $commandBus
            ->attach(MessageBus::EVENT_DISPATCH, $listener);

        $commandRouter
            ->route(CreateOrder::class)
            ->to(new CreateOrderHandler($eventBus))
            ->route(MakeReservation::class)
            ->to(new MakeReservationHandler($eventBus, $contents[\Config::AVAILABLE_SEATS], $contents[\Config::PRICE_PER_SEAT]))
            ->route(MakePayment::class)
            ->to(new MakePaymentHandler($eventBus))
            ->route(AddSeatsToWaitList::class)
            ->to(new AddSeatsToWaitListHandler($eventBus));

        $eventRouter
            ->route(OrderCreated::class)
            ->to($saga)
            ->route(SeatsReserved::class)
            ->to($saga)
            ->route(SeatsNotReserved::class)
            ->to($saga)
            ->route(PaymentAccepted::class)
            ->to($saga);

        $commandRouter
            ->attachToMessageBus($commandBus);

        $eventRouter
            ->attachToMessageBus($eventBus);

        return array_merge(
            $contents,
            [
                CommandRouter::class    => $commandRouter,
                CollectsMessages::class => $listener,
                StateRepository::class  => $stateRepository,
                EventBus::class         => $eventBus,
                CommandBus::class       => $commandBus,
            ]
        );
    }

    private function console(array $contents): array
    {
        $application       = new Application();
        $welcomeMessage    = new WelcomeMessage($contents[\Config::AVAILABLE_SEATS], $contents[\Config::PRICE_PER_SEAT]);
        $tableWithMessages = new TableWithMessages($contents[CollectsMessages::class]);
        $consoleCommand    = new PlaygroundCommand($contents[CommandBus::class], $tableWithMessages, $welcomeMessage);

        $application->add($consoleCommand);

        return array_merge(
            $contents,
            [
                WelcomeMessage::class    => $welcomeMessage,
                TableWithMessages::class => $tableWithMessages,
                PlaygroundCommand::class => $consoleCommand,
                Application::class       => $application,
            ]
        );
    }
}
