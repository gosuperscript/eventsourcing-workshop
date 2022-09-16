<?php

namespace Workshop\Domains\Wallet\Events;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class TokensDeposited implements SerializablePayload
{
    const DATE_TIME_FORMAT = 'Y-m-d H:i:s.uO';

    public function __construct(
        public readonly int $tokens,
        public readonly string $description,
        public readonly \DateTimeImmutable $transactedAt,
    ) {
    }

    public function toPayload(): array
    {
        return [
            'tokens' => $this->tokens,
            'description' => $this->description,
            'transacted_at' => $this->transactedAt->format(self::DATE_TIME_FORMAT)
        ];
    }

    public static function fromPayload(array $payload): static
    {
        return new static(
            $payload['tokens'],
            array_key_exists('description', $payload) ? $payload['description'] : 'unknown',
            \DateTimeImmutable::createFromFormat(self::DATE_TIME_FORMAT, $payload['transacted_at'])
        );
    }
}
