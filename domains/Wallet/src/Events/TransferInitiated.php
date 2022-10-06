<?php

namespace Workshop\Domains\Wallet\Events;

use Carbon\Carbon;
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
        public readonly Carbon $startedAt
    ) {
    }

    public function toPayload(): array
    {
        return [
            'transaction_id' => $this->transactionId->toString(),
            'receiving_wallet_id' => $this->receivingWalletId->toString(),
            'tokens' => $this->tokens,
            'description' => $this->description,
            'started_at' => $this->startedAt->jsonSerialize(),
        ];
    }

    public static function fromPayload(array $payload): static
    {
        return new static(
            TransactionId::fromString($payload['transaction_id']),
            WalletId::fromString($payload['receiving_wallet_id']),
            $payload['tokens'],
            $payload['description'],
            Carbon::parse($payload['started_at']),
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
