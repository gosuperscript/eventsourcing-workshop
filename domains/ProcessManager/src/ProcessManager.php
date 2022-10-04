<?php

namespace Workshop\Domains\ProcessManager;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageConsumer;
use EventSauce\EventSourcing\Serialization\SerializablePayload;

interface ProcessManager extends MessageConsumer
{
    public function startsOn(Message $message): bool;

    public function toPayload(): array;

    public function fromPayload(array $payload): void;
}