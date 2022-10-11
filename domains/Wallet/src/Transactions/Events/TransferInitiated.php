<?php

namespace Workshop\Domains\Wallet\Transactions\Events;

use DateTimeImmutable;
use EventSauce\EventSourcing\Serialization\SerializablePayload;
use Workshop\Domains\ProcessManager\HasProcessIds;
use Workshop\Domains\Wallet\Commands\TransferTokens;
use Workshop\Domains\Wallet\Transactions\TransactionId;
use Workshop\Domains\Wallet\WalletId;

class TransferInitiated implements SerializablePayload, HasProcessIds
{
    public function __construct(
        public readonly TransactionId $transactionId,
        public readonly WalletId $receivingWalletId,
        public readonly int $tokens,
        public readonly string $description,
        public readonly DateTimeImmutable $startedAt
    ) {
    }

    public static function fromCommand(TransferTokens $transferTokens, DateTimeImmutable $now): self
    {
        return new self($transferTokens->transactionId, $transferTokens->receivingWalletId, $transferTokens->tokens, $transferTokens->description, $now);
    }

    public function toPayload(): array
    {
        return [
            'transactionId' => $this->transactionId->toString(),
            'receivingWalletId' => $this->receivingWalletId->toString(),
            'tokens' => $this->tokens,
            'description' => $this->description,
            'startedAt' => $this->startedAt->format('Y-m-d H:i:s'),
        ];
    }

    public static function fromPayload(array $payload): static
    {
        return new self(
            TransactionId::fromString($payload['transactionId']),
            WalletId::fromString($payload['receivingWalletId']),
            $payload['tokens'],
            $payload['description'],
            DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $payload['startedAt'])
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
