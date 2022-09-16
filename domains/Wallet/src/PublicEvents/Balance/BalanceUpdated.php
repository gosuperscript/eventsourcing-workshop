<?php

namespace Workshop\Domains\Wallet\PublicEvents\Balance;

use Robertbaelde\PersistingMessageBus\PublicMessage;

class BalanceUpdated implements PublicMessage
{
    public function __construct(
        public readonly string $walletId,
        public readonly int $balance,
    )
    {

    }
    public function toPayload(): array
    {
        return [
            'walletId' => $this->walletId,
            'balance' => $this->balance,
        ];
    }

    public static function fromPayload(array $payload): static
    {
        return new self(
            $payload['walletId'],
            $payload['balance'],
        );
    }
}
