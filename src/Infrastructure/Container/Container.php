<?php

declare(strict_types=1);

namespace Infrastructure\Container;

use Application\Command\CreateOrder;
use Application\Command\CreateOrderHandler;
use Domain\Order\OrderRepository;
use Infrastructure\Console\PlaygroundCommand;
use Infrastructure\Persistence\EventSourcedOrderRepository;
use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\InMemoryEventStore;
use Prooph\EventStore\TransactionalActionEventEmitterEventStore;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\Plugin\Router\CommandRouter;
use Symfony\Component\Console\Application;

final class Container
{
    /** @var array */
    private $services;

    public function __construct()
    {
        $commandBus = new CommandBus();
        $router     = new CommandRouter();

        $eventStore = new TransactionalActionEventEmitterEventStore(
            new InMemoryEventStore(),
            new ProophActionEventEmitter()
        );

        $orderRepository = new EventSourcedOrderRepository($eventStore);

        $createOrderHandler = new CreateOrderHandler($orderRepository);
        $router->route(CreateOrder::class)
            ->to($createOrderHandler);

        $router->attachToMessageBus($commandBus);

        $command = new PlaygroundCommand($commandBus);

        $application = new Application();
        $application->add($command);

        $this->services = [
            CommandBus::class        => $commandBus,
            EventStore::class        => $eventStore,
            OrderRepository::class   => $orderRepository,
            PlaygroundCommand::class => $command,
            Application::class       => $application,
        ];
    }

    public function __invoke(string $service)
    {
        if (false === isset($this->services[$service])) {
            throw new \InvalidArgumentException(sprintf('Service "%s" does not exist.', $service));
        }

        return $this->services[$service];
    }
}
