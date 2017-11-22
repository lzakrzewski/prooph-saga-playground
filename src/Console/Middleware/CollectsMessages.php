<?php

declare(strict_types=1);

namespace Console\Middleware;

use Prooph\Common\Event\ActionEvent;
use Prooph\ServiceBus\MessageBus;

class CollectsMessages
{
    /** @var array */
    private $collectedMessages = [];

    public function __invoke(ActionEvent $actionEvent)
    {
        $message = $actionEvent->getParam(MessageBus::EVENT_PARAM_MESSAGE);

        $this->collectedMessages[] = $message;
    }

    public function all(): array
    {
        $messages = $this->collectedMessages;

        $this->collectedMessages = [];

        return $messages;
    }
}
