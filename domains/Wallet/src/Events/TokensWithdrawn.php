<?php

namespace Workshop\Domains\Wallet\Events;

final class TokensWithdrawn
{
    public function __construct(
        private readonly int $tokens,
        private readonly string $description = 'No message given',
        private readonly ?string $transacted_at = null,
    ) {
    }

    public function tokens(): int
    {
        return $this->tokens;
    }

    public function description(): ?string
    {
        return $this->description;
    }
}