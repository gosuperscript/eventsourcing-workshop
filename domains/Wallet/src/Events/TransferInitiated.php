<?php

namespace Workshop\Domains\Wallet\Events;

use EventSauce\EventSourcing\Serialization\SerializablePayload;
use Workshop\Domains\ProcessManager\HasProcessIds;
use Workshop\Domains\Wallet\Transactions\TransactionId;
use Workshop\Domains\Wallet\WalletId;

class TransferInitiated implements SerializablePayload, hasProcessIds
{

    public function __construct(
        public readonly TransactionId $transactionId,
        public readonly WalletId $receivingWalletId,
        public readonly int $tokens,
        public readonly string $description,
        public readonly \DateTimeImmutable $startedAt
    ) {
    }

    public function toPayload(): array
    {
        return [
            'transaction_id' => $this->transactionId->toString(),
            'receiving_wallet_id' => $this->receivingWalletId->toString(),
            'tokens' => $this->tokens,
            'description' => $this->description,
            'started_at' => $this->startedAt->format('Y-m-d H:i:s'),
        ];
    }

    public static function fromPayload(array $payload): static
    {
        return new static(
            TransactionId::fromString($payload['transaction_id']),
            WalletId::fromString($payload['receiving_wallet_id']),
            $payload['tokens'],
            $payload['description'],
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $payload['started_at']),
        );
    }

    public function getCorrelationId(): ?string
    {
        return $this->transactionId->toString();
    }

    public function getCausationId(): ?string
    {
        return null;
    }
}
