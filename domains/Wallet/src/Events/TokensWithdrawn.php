<?php

namespace Workshop\Domains\Wallet\Events;

use EventSauce\EventSourcing\Serialization\SerializablePayload;
use Workshop\Domains\Wallet\Transactions\TransactionId;

final class TokensWithdrawn implements SerializablePayload
{
    const DATE_TIME_FORMAT = 'Y-m-d H:i:s.uO';

    public function __construct(
        public readonly int $tokens,
        public readonly string $description,
        public readonly \DateTimeImmutable $transactedAt,
        public readonly ?TransactionId $transactionId
    ) {
    }

    public function toPayload(): array
    {
        return [
            'tokens' => $this->tokens,
            'description' => $this->description,
            'transacted_at' => $this->transactedAt->format(self::DATE_TIME_FORMAT),
            'transaction_id' => $this->transactionId?->toString()
        ];
    }

    public static function fromPayload(array $payload): static
    {
        return new static(
            $payload['tokens'],
            array_key_exists('description', $payload) ? $payload['description'] : 'unknown',
            \DateTimeImmutable::createFromFormat(self::DATE_TIME_FORMAT, $payload['transacted_at']),
            array_key_exists('transaction_id', $payload) && $payload['transaction_id'] !== null ? TransactionId::fromString($payload['transaction_id']) : null
        );
    }
}
