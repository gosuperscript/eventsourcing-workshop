<?php

namespace Workshop\Domains\Wallet\Events;

use EventSauce\EventSourcing\Serialization\SerializablePayload;
use Workshop\Domains\Wallet\WithdrawalFailureType;

class WithdrawalFailed implements SerializablePayload
{
    public function __construct(public readonly WithdrawalFailureType $reason)
    {

    }

    public static function becauseOfInsufficientFunds(): self
    {
        return new self(WithdrawalFailureType::InsufficientFunds);
    }

    public function toPayload(): array
    {
        return ['reason' => $this->reason->value];
    }

    public static function fromPayload(array $payload): static
    {
        return new self(
            WithdrawalFailureType::from($payload['reason'])
        );
    }
}
