<?php

namespace Workshop\Domains\Wallet\PublicEvents\Balance;

class BalanceTopic extends \Robertbaelde\PersistingMessageBus\BaseTopic
{
    public const BalanceUpdated = BalanceUpdated::class;

    public function getMessages(): array
    {
        return [
            'BalanceUpdated' => self::BalanceUpdated
        ];
    }

    public function getName(): string
    {
        return 'Balance';
    }
}