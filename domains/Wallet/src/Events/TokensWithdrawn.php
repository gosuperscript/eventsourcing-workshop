<?php

namespace Workshop\Domains\Wallet\Events;

final class TokensWithdrawn
{
    public function __construct(public readonly int $tokens)
    {
    }
}
