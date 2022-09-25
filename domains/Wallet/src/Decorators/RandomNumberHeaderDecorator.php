<?php

namespace Workshop\Domains\Wallet\Decorators;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDecorator;

class RandomNumberHeaderDecorator implements MessageDecorator
{
    public function decorate(Message $message): Message
    {
        return $message->withHeader('x-decorator-number', rand(0, PHP_INT_MAX));
    }
}