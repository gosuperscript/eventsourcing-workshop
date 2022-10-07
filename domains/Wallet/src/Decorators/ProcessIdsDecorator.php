<?php

namespace Workshop\Domains\Wallet\Decorators;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDecorator;
use Workshop\Domains\ProcessManager\hasProcessIds;
use Workshop\Domains\ProcessManager\ProcessHeaders;

class ProcessIdsDecorator implements MessageDecorator
{

    public function __construct()
    {
    }

    public function decorate(Message $message): Message
    {
        $event = $message->payload();
        if (!$event instanceof hasProcessIds) {
            return $message;
        }

        return $message->withHeaders([
            ProcessHeaders::CORRELATION_ID => $event->getCorrelationId(),
            ProcessHeaders::CAUSATION_ID => $event->getCausationId(),
        ]);
    }
}
