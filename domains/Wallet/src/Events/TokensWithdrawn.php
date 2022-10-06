<?php

namespace Workshop\Domains\Wallet\Events;

use Carbon\Carbon;
use EventSauce\EventSourcing\Serialization\SerializablePayload;
use Workshop\Domains\ProcessManager\HasProcessIds;
use Workshop\Domains\Wallet\Transactions\TransactionId;

final class TokensWithdrawn implements SerializablePayload, HasProcessIds
{
    public function __construct(
        public readonly int            $tokens,
        public readonly Carbon         $transactedAt,
        public readonly ?TransactionId $transactionId,
        public readonly string         $description = 'unknown',
    ) {
    }

    public function toPayload(): array
    {
        return [
            'tokens' => $this->tokens,
            'transacted_at' => $this->transactedAt->jsonSerialize(),
            'description' => $this->description,
        ];
    }

    public static function fromPayload(array $payload): static
    {
        return new static(
            tokens: $payload['tokens'],
            transactedAt: Carbon::parse($payload['transacted_at']),
            transactionId: TransactionId::fromString($payload['transaction_id']),
            description: $payload['description'],
        );
    }

    public function getCorrelationId(): ?string
    {
        return $this->transactionId?->toString();
    }

    public function getCausationId(): ?string
    {
        return null;
    }
}
