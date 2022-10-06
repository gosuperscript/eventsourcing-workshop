<?php

namespace Workshop\Domains\FraudDetection\Events;

use EventSauce\EventSourcing\Serialization\SerializablePayload;
use Workshop\Domains\ProcessManager\HasProcessIds;

class TransactionApproved implements SerializablePayload, HasProcessIds
{
    public function __construct(
        public readonly string $transactionId
    ){

    }
    public function getCorrelationId(): ?string
    {
        return $this->transactionId;
    }

    public function getCausationId(): ?string
    {
        return null;
    }

    public function toPayload(): array
    {
        return [
            'transactionId' => $this->transactionId,
        ];
    }

    public static function fromPayload(array $payload): static
    {
        return new self($payload['transactionId']);
    }
}
