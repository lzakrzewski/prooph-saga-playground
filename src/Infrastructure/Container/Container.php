<?php

declare(strict_types=1);

namespace Infrastructure\Container;

use Application\Command\CreateOrder;
use Application\Command\CreateOrderHandler;
use Application\Middleware\CollectsMessages;
use Infrastructure\Console\PlaygroundCommand;
use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\InMemoryEventStore;
use Prooph\EventStore\TransactionalActionEventEmitterEventStore;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\Router\CommandRouter;
use Symfony\Component\Console\Application;

final class Container
{
    /** @var array */
    private $services = [];

    public function __construct()
    {
        $this->registerEventStore();
        $this->registerCommandHandlers();
        $this->registerCommandBus();
        $this->registerConsoleCommand();
        $this->registerApplication();
    }

    public function __invoke(string $service)
    {
        if (false === isset($this->services[$service])) {
            throw new \InvalidArgumentException(sprintf('Service "%s" does not exist.', $service));
        }

        return $this->services[$service];
    }

    private function registerEventStore()
    {
        $this->services[EventStore::class] = new TransactionalActionEventEmitterEventStore(
            new InMemoryEventStore(),
            new ProophActionEventEmitter()
        );
    }

    private function registerCommandHandlers()
    {
        $this->services[CommandRouter::class] = new CommandRouter();

        /* @var EventBus $eventBus */
        $this->services[EventBus::class]   = $eventBus   = new EventBus(new ProophActionEventEmitter());
        $middleware                        = new CollectsMessages();

        $eventBus->attach(MessageBus::EVENT_DISPATCH, $middleware);

        $this->services[CollectsMessages::class] = $middleware;

        $this->services[CreateOrderHandler::class]= new CreateOrderHandler($eventBus);

        $this->services[CommandRouter::class]->route(CreateOrder::class)
            ->to($this->services[CreateOrderHandler::class]);
    }

    private function registerCommandBus()
    {
        $this->services[CommandBus::class] = new CommandBus();
        $this->services[CommandBus::class]->attach(MessageBus::EVENT_DISPATCH, $this->services[CollectsMessages::class]);
        $this->services[CommandRouter::class]->attachToMessageBus($this->services[CommandBus::class]);
    }

    private function registerConsoleCommand()
    {
        $this->services[PlaygroundCommand::class] = new PlaygroundCommand($this->services[CommandBus::class], $this->services[CollectsMessages::class]);
    }

    private function registerApplication()
    {
        $this->services[Application::class] = new Application();
        $this->services[Application::class]->add($this->services[PlaygroundCommand::class]);
    }
}
