<?php

namespace Workshop\Domains\Wallet;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDecorator;

class RandomNumberMessageHeaderAdder implements MessageDecorator
{
    const KEY = 'randomNumber';

    public function decorate(Message $message): Message
    {
        return $message->withHeader(self::KEY, random_int(10000, 99999));
    }
}