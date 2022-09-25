<?php

namespace Workshop\Domains\Wallet\Events;

final class TokensWithdrawn
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