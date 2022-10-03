<?php

namespace Workshop\Domains\ProcessManager;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageConsumer;
use EventSauce\EventSourcing\Serialization\SerializablePayload;

interface ProcessManager extends SerializablePayload, MessageConsumer
{
    public function startsOn(Message $message): bool;
}
