<?php

namespace Workshop\Domains\Wallet\Tests\Utilities;

use Workshop\Domains\Wallet\ReadModels\Balance;
use Workshop\Domains\Wallet\ReadModels\BalanceReadModelRepository;
use Workshop\Domains\Wallet\WalletId;

class InMemoryBalanceRepository implements BalanceReadModelRepository
{
    private array $balances = [];

    public function getBalanceFor(WalletId $walletId): Balance
    {
        return $this->balances[$walletId->toString()]
            ?? $this->balances[$walletId->toString()] = $this->newBalanceFor($walletId);
    }

    private function newBalanceFor(WalletId $walletId): Balance
    {
        return new Balance([
            'wallet_id' => $walletId->toString(),
            'balance' => 0
        ]);
    }

    public function save(Balance $balance): void
    {
        $this->balances[$balance->wallet_id] = $balance;
    }
}