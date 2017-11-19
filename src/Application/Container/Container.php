<?php

declare(strict_types=1);

namespace Application\Container;

use Application\Command\CreateOrder;
use Application\Command\CreateOrderHandler;
use Console\PlaygroundCommand;
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
        $command = new PlaygroundCommand();

        $application = new Application();
        $application->add($command);

        $commandBus = new CommandBus();
        $router     = new CommandRouter();

        $eventStore = new TransactionalActionEventEmitterEventStore(
            new InMemoryEventStore(),
            new ProophActionEventEmitter()
        );

        $userRepository = new EventSourcedOrderRepository($eventStore);

        $createOrderHandler = new CreateOrderHandler($userRepository);
        $router->route(CreateOrder::class)
            ->to($createOrderHandler);

        $router->attachToMessageBus($commandBus);

        $this->services = [
            CommandBus::class        => $commandBus,
            EventStore::class        => $eventStore,
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
