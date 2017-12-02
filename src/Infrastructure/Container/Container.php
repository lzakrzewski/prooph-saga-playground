<?php

declare(strict_types=1);

namespace Infrastructure\Container;

use Infrastructure\Console\Display\Questions;
use Infrastructure\Console\Display\TableWithMessages;
use Infrastructure\Console\Display\WelcomeMessage;
use Infrastructure\Console\PlaygroundCommand;
use Infrastructure\Listener\MessageCollector;
use Infrastructure\Persistence\InMemoryStateRepository;
use Messaging\Command\AddSeatsToWaitList;
use Messaging\Command\Handler\AddSeatsToWaitListHandler;
use Messaging\Command\Handler\MakePaymentHandler;
use Messaging\Command\Handler\MakeReservationHandler;
use Messaging\Command\Handler\PlaceOrderHandler;
use Messaging\Command\MakePayment;
use Messaging\Command\MakeReservation;
use Messaging\Command\PlaceOrder;
use Messaging\Event\OrderPlaced;
use Messaging\Event\PaymentAccepted;
use Messaging\Event\SeatsNotReserved;
use Messaging\Event\SeatsReserved;
use Messaging\ProcessManager\OrderProcessManager;
use Messaging\ProcessManager\StateRepository;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\Router\CommandRouter;
use Prooph\ServiceBus\Plugin\Router\EventRouter;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\QuestionHelper;

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
        $listener         = new MessageCollector();
        $commandBus       = new CommandBus();
        $eventBus         = new EventBus();
        $stateRepository  = new InMemoryStateRepository();
        $saga             = new OrderProcessManager($commandBus, $eventBus, $stateRepository);

        $eventBus
            ->attach(MessageBus::EVENT_DISPATCH, $listener, 1);
        $commandBus
            ->attach(MessageBus::EVENT_DISPATCH, $listener, 1);

        $commandRouter
            ->route(PlaceOrder::class)
            ->to(new PlaceOrderHandler($eventBus))
            ->route(MakeReservation::class)
            ->to(new MakeReservationHandler($eventBus, $contents[\Config::AVAILABLE_SEATS], $contents[\Config::PRICE_PER_SEAT]))
            ->route(MakePayment::class)
            ->to(new MakePaymentHandler($eventBus))
            ->route(AddSeatsToWaitList::class)
            ->to(new AddSeatsToWaitListHandler($eventBus));

        $eventRouter
            ->route(OrderPlaced::class)
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
                MessageCollector::class => $listener,
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
        $questionHelper    = new QuestionHelper();
        $questions         = new Questions($contents[CommandBus::class], $questionHelper);
        $tableWithMessages = new TableWithMessages($contents[MessageCollector::class]);
        $consoleCommand    = $application
            ->add(new PlaygroundCommand($welcomeMessage, $questions, $tableWithMessages));

        return array_merge(
            $contents,
            [
                WelcomeMessage::class    => $welcomeMessage,
                Questions::class         => $questions,
                TableWithMessages::class => $tableWithMessages,
                PlaygroundCommand::class => $consoleCommand,
                Application::class       => $application,
            ]
        );
    }
}
