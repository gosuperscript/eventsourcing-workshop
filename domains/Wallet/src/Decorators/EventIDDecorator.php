<?php

namespace Workshop\Domains\Wallet\Decorators;

use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDecorator;
use Ramsey\Uuid\Uuid;

class EventIDDecorator implements MessageDecorator
{

    public function decorate(Message $message): Message
    {
        return $message->withHeader(Header::EVENT_ID, Uuid::uuid4()->toString());
    }
}
