<?php

declare(strict_types=1);

namespace Infrastructure\Middleware;

use Messaging\Command;
use Messaging\DomainEvent;
use Messaging\Message;
use Prooph\Common\Event\ActionEvent;
use Prooph\ServiceBus\MessageBus;

class CollectsMessages
{
    /** @var array */
    private $collectedMessages = [];

    public function __invoke(ActionEvent $actionEvent)
    {
        $message = $actionEvent->getParam(MessageBus::EVENT_PARAM_MESSAGE);

        if (false === $message instanceof Message) {
            return;
        }

        $this->collectMessage($actionEvent->getParam(MessageBus::EVENT_PARAM_MESSAGE));
    }

    public function all(): array
    {
        $messages = $this->collectedMessages;

        $this->collectedMessages = [];

        return $messages;
    }

    private function collectMessage(Message $message)
    {
        $collectedMessages = $this->collectedMessages;

        if (
            false === empty($collectedMessages)
            && end($collectedMessages) instanceof DomainEvent
            && $message instanceof Command
        ) {
            $event = array_pop($this->collectedMessages);

            $this->collectedMessages = array_merge(
                $this->collectedMessages,
                [$message, $event]
            );

            return;
        }

        $this->collectedMessages[] = $message;
    }
}
