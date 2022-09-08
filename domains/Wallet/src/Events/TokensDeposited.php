<?php

namespace Workshop\Domains\Wallet\Events;

final class TokensDeposited
{
    public function __construct(
        public readonly int $tokens
    ) {
    }
}
