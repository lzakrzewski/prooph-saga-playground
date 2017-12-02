<?php

declare(strict_types=1);

namespace Infrastructure\Listener;

use Messaging\Message;
use Prooph\Common\Event\ActionEvent;
use Prooph\ServiceBus\MessageBus;

class MessageCollector
{
    /** @var array */
    private $collectedMessages = [];

    public function __invoke(ActionEvent $actionEvent): void
    {
        $message = $actionEvent->getParam(MessageBus::EVENT_PARAM_MESSAGE);

        if (false === $message instanceof Message) {
            return;
        }

        $this->collectedMessages[] = $message;
    }

    public function all(): array
    {
        return  $this->collectedMessages;
    }
}
