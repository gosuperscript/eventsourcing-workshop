<?php

namespace Workshop\Domains\Wallet\Events;

final class TokensOverdrawAttempted
{
    public function __construct(
        private readonly int $at
    ) {
    }

    public function at(): int
    {
        return $this->at;
    }
}