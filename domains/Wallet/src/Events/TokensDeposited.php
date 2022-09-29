<?php

namespace Workshop\Domains\Wallet\Events;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class TokensDeposited
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

    public function description(): string
    {
        return $this->description;
    }
}