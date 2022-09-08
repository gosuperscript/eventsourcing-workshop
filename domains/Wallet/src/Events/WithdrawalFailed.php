<?php

namespace Workshop\Domains\Wallet\Events;

use Workshop\Domains\Wallet\WithdrawalFailureType;

class WithdrawalFailed
{
    public function __construct(public readonly WithdrawalFailureType $reason)
    {}

    public static function becauseOfInsufficientFunds(): self
    {
        return new self(WithdrawalFailureType::InsufficientFunds);
    }
}
