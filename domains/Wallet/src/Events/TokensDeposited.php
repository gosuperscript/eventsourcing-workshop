<?php

namespace Workshop\Domains\Wallet\Events;

use Carbon\Carbon;
use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class TokensDeposited implements SerializablePayload
{
    public function __construct(
        public readonly int $tokens,
        public readonly Carbon $transacted_at,
        public readonly string $description = 'unknown',
    ) {
    }

    public function toPayload(): array
    {
        return [
            'tokens' => $this->tokens,
            'transacted_at' => $this->transacted_at->jsonSerialize(),
            'description' => $this->description,
        ];
    }

    public static function fromPayload(array $payload): static
    {
        return new static(
            tokens: $payload['tokens'],
            transacted_at: Carbon::parse($payload['transacted_at']),
            description: $payload['description'],
        );
    }
}
