<?php

namespace Workshop\Domains\Wallet\Events;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class TokensDeposited
{
    public function __construct(
        private readonly int $tokens
    ) {
    }

    public function tokens(): int
    {
        return $this->tokens;
    }
}