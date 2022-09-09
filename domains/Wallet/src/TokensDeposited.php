<?php

namespace Workshop\Domains\Wallet;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

class TokensDeposited implements SerializablePayload
{
    private int $amountOfTokens;

    public function __construct(int $amountOfTokens)
    {
        $this->amountOfTokens = $amountOfTokens;
    }

    public function toPayload(): array
    {
        return ['amountOfTokens' => $this->amountOfTokens];
    }

    public static function fromPayload(array $payload): static
    {
        return new self($payload['amountOfTokens']);
    }
}