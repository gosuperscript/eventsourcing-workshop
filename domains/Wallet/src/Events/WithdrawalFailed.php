<?php

namespace Workshop\Domains\Wallet\Events;

use EventSauce\EventSourcing\Serialization\SerializablePayload;
use Workshop\Domains\ProcessManager\HasProcessIds;
use Workshop\Domains\Wallet\Transactions\TransactionId;
use Workshop\Domains\Wallet\WithdrawalFailureType;

final class WithdrawalFailed implements SerializablePayload, HasProcessIds
{
    public function __construct(
        public readonly WithdrawalFailureType $reason,
        public readonly ?TransactionId $transactionId = null,
    )
    {}

    public static function becauseOfInsufficientFunds(?TransactionId $transactionId = null): self
    {
        return new self(
            WithdrawalFailureType::InsufficientFunds,
            $transactionId
        );
    }

    public function toPayload(): array
    {
        return [
            'reason' => $this->reason->value,
            'transaction_id' => $this->transactionId?->toString(),
        ];
    }

    public static function fromPayload(array $payload): static
    {
        return new self(
            WithdrawalFailureType::from($payload['reason']),
            array_key_exists('transaction_id', $payload) && $payload['transaction_id'] !== null ? TransactionId::fromString($payload['transaction_id']) : null
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
